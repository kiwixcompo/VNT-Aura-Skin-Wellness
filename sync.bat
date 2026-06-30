@echo off
title VNT Aura Skin ^& Wellness - Auto-Deploy Sync
color 0A

echo.
echo  ==============================================================
echo                    VNT AURA SKIN ^& WELLNESS                      
echo                       AUTO-DEPLOY SYNC TOOL                      
echo  ==============================================================
echo.
echo  Repository: https://github.com/kiwixcompo/VNT-Aura-Skin-Wellness
echo  Live Site:  https://vntauraskinandwellness.com
echo  ==============================================================
echo.

cd /d "%~dp0"

REM Quick repository setup check
git remote get-url origin >nul 2>&1
if errorlevel 1 (
    echo  [SETUP] Connecting to repository...
    git remote add origin https://github.com/kiwixcompo/VNT-Aura-Skin-Wellness.git
    git branch -M main
)

echo  [SYNC] Pulling latest changes...
git pull origin main --no-edit >nul 2>&1

echo  [SYNC] Adding your changes...
git add .

git diff --cached --quiet
if errorlevel 1 (
    REM Get timestamp
    for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
    set "timestamp=%dt:~0,4%-%dt:~4,2%-%dt:~6,2% %dt:~8,2%:%dt:~10,2%"
    
    echo  [SYNC] Committing changes...
    git commit -m "Auto-sync: %timestamp%" >nul
    
    echo  [SYNC] Uploading to GitHub...
    git push origin main >nul 2>&1
    
    if errorlevel 1 (
        echo.
        echo  [ERROR] SYNC FAILED - Check your internet connection or GitHub access
        echo.
        timeout /t 5 >nul
        exit /b 1
    )
    
    echo.
    echo  [SUCCESS] Changes uploaded to GitHub!
    echo.
) else (
    echo.
    echo  [INFO] No local changes detected. Proceeding to sync live site...
    echo.
)

echo  [DEPLOY] Triggering auto-deployment to live site...

REM Trigger manual deployment (Always runs to ensure public_html is synced)
curl -s "https://vntauraskinandwellness.com/deploy.php?manual=true" >nul 2>&1

if errorlevel 1 (
    echo  [WARNING] Manual deployment trigger failed - Check your server
) else (
    echo  [SUCCESS] Live site deployment triggered successfully!
)

echo.
echo  Your changes will be live at: https://vntauraskinandwellness.com
echo  View repository: https://github.com/kiwixcompo/VNT-Aura-Skin-Wellness
echo.

echo  Press any key to close...
pause >nul
