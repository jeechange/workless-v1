:start
@ECHO OFF
cls
@echo; 
@echo; 
@echo; 
@echo; 
@echo;
@echo                             ╔========================╗
@echo                              广西九程软创科技有限公司 
@echo                             ╚========================╝
@echo                           将数据库中的字段映射成yml和Entity
@echo; 
@echo; 
@echo; 
@echo; 
@echo; 
@echo 请输入关键词，输入0退出,1全部,或者输入表名
set/p filter= [0/1/*]
if %filter% == 0 ( exit )


SET dppath=%~dp0
set consolepath=%dppath:~0,-4%app\console

if %filter%== 1 ( goto nofilter
 ) else ( goto hasfilter )

:nofilter
@echo on
php "%consolepath%" "--ansi" "orm:convert-mapping" "--force" "--from-database" "yml"
php "%consolepath%" "--ansi" "orm:generate-entities" 
@echo off
goto eof
:hasfilter
@echo on
php "%consolepath%" "--ansi" "orm:convert-mapping" "--force" "--from-database" "yml" "--filter=%filter%"
php "%consolepath%" "--ansi" "orm:generate-entities" "--filter=%filter%"
@echo off
:eof
@echo 输入1再次执行，其它任意键退出
set/p wait= 
if %wait%==1 ( goto start )
pause;


