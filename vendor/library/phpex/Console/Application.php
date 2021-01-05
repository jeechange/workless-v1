<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Console;

use Symfony\Component\Console\Application as baseApplication;
use phpex\Library\MainInterface;
use phpex\Library\Main;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

/**
 * Description of Application
 *
 * @author Administrator
 */
class Application extends baseApplication {

    private $main;
    private $commandsRegistered = false;

    public function __construct(MainInterface $main) {
        parent::__construct("phpex Command Line Interface", Main::VERSION);
        $this->main = $main;
    }

    /**
     * @return Main;
     */
    public function getMain() {
        return $this->main;
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int     0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output) {
        $this->main->boot();
        if (!$this->commandsRegistered) {
            $conn = $input->getParameterOption("--conn", "default");
            $helperSet = ConsoleRunner::createHelperSet(DM($conn)->getManager());
            $this->setCatchExceptions(true);
            $this->setHelperSet($helperSet);
            ConsoleRunner::addCommands($this);
            $this->main->addCommands($this);
            $this->commandsRegistered = true;
        }
        return parent::doRun($input, $output);
    }

}
