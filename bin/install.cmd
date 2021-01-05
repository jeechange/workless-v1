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
@echo                             将资源文件安装到www/public中
@echo; 
@echo; ho; 
@echo; 
@echo; 
@echo 请输入关键词，输入0退出,输入1全部,其它如home,admin,jeesell,vshop,则只安装指定; 
set/p filter= [0/1/*]
if %filter% == 0 ( exit )

SET dppath=%~dp0
set consolepath=%dppath:~0,-4%app\console

if %filter% == 1 ( goto nofilter
 ) else ( goto hasfilter )

:nofilter
@echo on
php "%consolepath%" "--ansi" "Asset:install"
:eof
:hasfilter
@echo on
php "%consolepath%" "--ansi" "Asset:install" "--filter=%filter%"
@echo off
:eof
