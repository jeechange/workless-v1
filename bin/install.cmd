:start
@ECHO OFF
cls
@echo; 
@echo; 
@echo; 
@echo; 
@echo;
@echo                             �X========================�[
@echo                              �����ų����Ƽ����޹�˾ 
@echo                             �^========================�a
@echo                             ����Դ�ļ���װ��www/public��
@echo; 
@echo; ho; 
@echo; 
@echo; 
@echo ������ؼ��ʣ�����0�˳�,����1ȫ��,������home,admin,jeesell,vshop,��ֻ��װָ��; 
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
