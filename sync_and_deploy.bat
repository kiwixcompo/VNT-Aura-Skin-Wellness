@echo off
setlocal

echo ===================================================
echo VNT Aura Skin - Local to Live Deployment Sync
echo ===================================================
echo.

:: 1. Prompt for an active change statement
set /p COMMIT_MSG="Enter commit message (e.g., 'Updated homepage layout'): "
if "%COMMIT_MSG%"=="" set COMMIT_MSG="Auto-sync changes from local development"

echo.
echo [1/3] Staging changes locally...
git add .

echo.
echo [2/3] Committing updates...
git commit -m "%COMMIT_MSG%"

echo.
echo [3/3] Pushing to GitHub (origin/main)...
git push origin main

if %ERRORLEVEL% neq 0 (
    echo.
    echo ERROR: Failed to push to GitHub. Aborting live deployment trigger.
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo ===================================================
echo GitHub Sync Complete! Triggering Live Server Deployment...
echo ===================================================

:: Setup Webhook URL
:: Remember to upload deploy_server.php to your live /home/vntauras/public_html folder first!
set WEBHOOK_URL="https://vntauraskinandwellness.com/deploy_server.php?token=vnt_deploy_token_2026"

:: Trigger Webhook silently using curl
curl -sS %WEBHOOK_URL% > deploy_log.txt

echo.
echo Deployment triggered! Server response logged in deploy_log.txt.
echo ===================================================
pause
