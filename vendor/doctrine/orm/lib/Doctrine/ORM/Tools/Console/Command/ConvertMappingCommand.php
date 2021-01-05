<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ORM\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Tools\Console\MetadataFilter;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command to convert your mapping information between the various formats.
 *
 * @link    www.doctrine-project.org
 * @since   2.0
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 */
class ConvertMappingCommand extends Command {

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setName('orm:convert-mapping')
                ->setAliases(array('orm:convert:mapping'))
                ->setDescription('Convert mapping information between supported formats.')
                ->setDefinition(array(
                    new InputOption(
                            'filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A string pattern used to match entities that should be processed.'
                    ),
                    new InputArgument(
                            'to-type', InputArgument::OPTIONAL, 'The mapping type to be converted.'
                    ),
                    new InputOption(
                            'force', null, InputOption::VALUE_NONE, 'Force to overwrite existing mapping files.'
                    ),
                    new InputOption(
                            'from-database', null, null, 'Whether or not to convert mapping information from existing database.'
                    ),
                    new InputOption(
                            'extend', null, InputOption::VALUE_OPTIONAL, 'Defines a base class to be extended by generated entity classes.'
                    ),
                    new InputOption(
                            'num-spaces', null, InputOption::VALUE_OPTIONAL, 'Defines the number of indentation spaces', 4
                    ),
                    new InputOption(
                            'namespace', null, InputOption::VALUE_OPTIONAL, 'Defines a namespace for the generated entity classes, if converted from database.'
                    ),
                    new InputOption(
                            'conn', null, InputOption::VALUE_OPTIONAL, 'Defines a connection for the generated entity classes, if converted from database.'
                    ),
                ))
                ->setHelp(<<<EOT
Convert mapping information between supported formats.

This is an execute <info>one-time</info> command. It should not be necessary for
you to call this method multiple times, especially when using the <comment>--from-database</comment>
flag.

Converting an existing database schema into mapping files only solves about 70-80%
of the necessary mapping information. Additionally the detection from an existing
database cannot detect inverse associations, inheritance types,
entities with foreign keys as primary keys and many of the
semantical operations on associations such as cascade.

<comment>Hint:</comment> There is no need to convert YAML or XML mapping files to annotations
every time you make changes. All mapping drivers are first class citizens
in Doctrine 2 and can be used as runtime mapping for the ORM.

<comment>Hint:</comment> If you have a database with tables that should not be managed
by the ORM, you can use a DBAL functionality to filter the tables and sequences down
on a global level:

    \$config->setFilterSchemaAssetsExpression(\$regexp);
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getHelper('em')->getEntityManager();
        $params = $em->getConnection()->getParams();
        list($namespaces, $paths) = each($params["paths"]);
        if ($input->getOption('from-database') === true) {
            $databaseDriver = new DatabaseDriver(
                    $em->getConnection()->getSchemaManager()
            );

            $em->getConfiguration()->setMetadataDriverImpl(
                    $databaseDriver
            );

            if (($namespace = $input->getOption('namespace')) !== null) {
                $databaseDriver->setNamespace($namespace);
            } else {
                $databaseDriver->setNamespace($namespaces."\\Entity\\");
            }
        }

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);
        $metadata = $cmf->getAllMetadata();
        $metadata = MetadataFilter::filter($metadata, $input->getOption('filter'));

        $destPath = $paths? : main()->getAppRoot() . "/Admin/Conf/doctrine";        

        // Process destination directory
        if (!is_dir($destPath)) {
            mkdir($destPath, 777, true);
        }
        $destPath = realpath($destPath);

        if (!file_exists($destPath)) {
            throw new \InvalidArgumentException(
            sprintf("Mapping destination directory '<info>%s</info>' does not exist.", $input->getArgument('dest-path'))
            );
        }

        if (!is_writable($destPath)) {
            throw new \InvalidArgumentException(
            sprintf("Mapping destination directory '<info>%s</info>' does not have write permissions.", $destPath)
            );
        }

        $toType = strtolower($input->getArgument('to-type'));

        $exporter = $this->getExporter($toType, $destPath);
        $exporter->setOverwriteExistingFiles($input->getOption('force'));

        if ($toType == 'annotation') {
            $entityGenerator = new EntityGenerator();
            $exporter->setEntityGenerator($entityGenerator);

            $entityGenerator->setNumSpaces($input->getOption('num-spaces'));

            if (($extend = $input->getOption('extend')) !== null) {
                $entityGenerator->setClassToExtend($extend);
            }
        }

        if (count($metadata)) {
            $params = $em->getConnection()->getParams();
            if (strlen($params["prefix"]) > 0) {
                $prefix = substr(ucfirst($params["prefix"]), 0, -1);
            } else {
                $prefix = "";
            }
            foreach ($metadata as $class) {
                if ($prefix) {
                    $names = $class->namespace ? substr($class->name, strlen($class->namespace) + 1) : $class->name;
                    $class->name = preg_replace("/^" . $prefix . "(.*)/", '$1', $names);
                    $class->name = $class->namespace ? $class->namespace . "\\" . $class->name : $class->name;
                    $class->rootEntityName = $class->name;
                    $class->table["name"] = preg_replace("/^" . $params["prefix"] . $params["prefix"] . "(.*)/", '$1', $class->table["name"]);
                }
                $output->writeln(sprintf('Processing entity "<info>%s</info>"', $class->name));
            }



            $exporter->setMetadata($metadata);
            $exporter->export();

            $output->writeln(PHP_EOL . sprintf(
                            'Exporting "<info>%s</info>" mapping information to "<info>%s</info>"', $toType, $destPath
            ));
        } else {
            $output->writeln('No Metadata Classes to process.');
        }
    }

    /**
     * @param string $toType
     * @param string $destPath
     *
     * @return \Doctrine\ORM\Tools\Export\Driver\AbstractExporter
     */
    protected function getExporter($toType, $destPath) {
        $cme = new ClassMetadataExporter();

        return $cme->getExporter($toType, $destPath);
    }

}
