@echo off
echo =========================================
echo VNT Aura Skin - GitHub Auto Sync
echo =========================================

echo.
echo [1/3] Adding changes to Git...
git add .

echo.
echo [2/3] Committing changes...
git commit -m "Auto-sync changes from local development"

echo.
echo [3/3] Pushing to GitHub repository...
git push origin main

echo.
echo =========================================
echo Sync Complete!
echo =========================================
pause
