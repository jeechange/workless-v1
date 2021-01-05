<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/9/13
 * Time: 15:42
 */

namespace phpex\Command;

use phpex\Util\Crypt\Des3;
use phpex\Util\ORG\PHPZip;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AssetCdnCommand extends Command {

    protected function configure() {
        $this->setName('Asset:cdn')
            ->setDescription('Asset upload to cdn');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {


        $cdnConfig = C("cdn");
        $assetConfig = C("asset");
        $this->appDir = main()->getAppRoot();
        $basePath = Q()->server->get("SCRIPT_FILENAME");
        $basePath = dirname(dirname($basePath)) . "/www/" . $assetConfig["basedir"];
        $basePath = str_replace("\\", "/", $basePath);


        if (is_file($basePath . ".zip")) unlink($basePath . ".zip");

        $phpZip = new PHPZip();
        $output->writeln("create zip: <comment>" . $basePath . ".zip</comment>");
        $phpZip->Zip($basePath, $basePath . ".zip", array($basePath . "/core"));


        $des = new Des3($cdnConfig["key"], $cdnConfig["iv"]);


        $md5Str = md5_file(dirname($basePath) . "/" . $assetConfig["basedir"] . ".zip");

        $post = array(
            "name" => $cdnConfig["name"],
            "checkstr" => $des->encrypt($md5Str),
            "file" => "@" . dirname($basePath) . "/" . $assetConfig["basedir"] . ".zip",
        );

        $output->writeln("upload zip: <comment>" . $basePath . ".zip</comment>");
        $res = curl_post($cdnConfig["url"] . "/upload.php", $post);
        $output->writeln($res);
    }

}
