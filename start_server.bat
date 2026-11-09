@echo off
title Spordha Diagnostics Ltd. - PKI & Medical Web Portal
echo =========================================================================
echo    SPORDHA DIAGNOSTICS LTD. - SECURE LABORATORY PKI WEB PORTAL
echo    Track 2: PKI and Secure Communication ^| East West University
echo =========================================================================
echo.
echo Starting local PHP development server...
echo Pointing to: http://127.0.0.1:8080
echo.

if exist "C:\xampp\php\php.exe" (
    set PHP_EXE="C:\xampp\php\php.exe"
) else (
    set PHP_EXE=php
)

start "" http://127.0.0.1:8080
%PHP_EXE% -S 127.0.0.1:8080
pause
