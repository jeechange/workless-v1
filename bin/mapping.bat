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
@echo                           �����ݿ��е��ֶ�ӳ���yml��Entity
@echo; 
@echo; 
@echo; 
@echo; 
@echo; 
@echo ������ؼ��ʣ�����0�˳�,1ȫ��,�����������
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
@echo ����1�ٴ�ִ�У�����������˳�
set/p wait= 
if %wait%==1 ( goto start )
pause;


