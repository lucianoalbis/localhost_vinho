@ECHO OFF
CD psx\vendor\psx
FOR /r %%i IN (*.php) DO CALL :check "%%i"

:check
php -l %~1
IF %ERRORLEVEL% NEQ 0 (
	EXIT 1
)
