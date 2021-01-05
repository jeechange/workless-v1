<meta charset="utf-8" />
<form method="post">
<input type="text" name="code" size="5"/>
<input type="submit" name="submit" value="ok"/>
<pre>
1.把资源丢到www
Asset:install

2.根据数据库生成yml 
orm:convert-mapping --force --from-database yml

3. 根据yml生成实体
orm:generate-entities

4.根据yml和实体更新数据库信息
orm:schema-tool:update --force

</pre>
</form>
<?php
if (!$_POST) exit;
set_time_limit(0);
$_SERVER['argv'][0]='shell.php';
if($_POST['code']=='1') {
  $_SERVER['argv'][1]="Asset:install";
} else if($_POST['code']=='2') {
  $_SERVER['argv'][1]="orm:convert-mapping";
  $_SERVER['argv'][2]="--force";
  $_SERVER['argv'][3]="--from-database";
  $_SERVER['argv'][4]="yml";
} else if($_POST['code']=='3') {
  $_SERVER['argv'][1]="orm:generate-entities";
} else if($_POST['code']=='4') {
  $_SERVER['argv'][1]="orm:schema-tool:update";
  $_SERVER['argv'][2]="--force";
}
include_once '../app/start.php';
use phpex\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), 'dev');
$debug = !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

$main = new appMain($env, $debug);
$application = new Application($main);
$application->run($input);

?>
