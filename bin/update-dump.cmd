SET dppath=%~dp0
set consolepath=%dppath:~0,-4%app\console

php "%consolepath%" "--ansi" "orm:schema-tool:update" "--dump-sql"
