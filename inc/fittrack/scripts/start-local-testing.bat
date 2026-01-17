@echo off
echo ========================================
echo   FITTRACK PRO - TEST ENVIRONMENT
echo   Demarrage de l'environnement local
echo ========================================
echo.

REM Couleurs pour Windows
color 0A

echo [1/4] Demarrage d'Apache XAMPP...
echo.
start "" "C:\xampp\apache\bin\httpd.exe"
timeout /t 3 /nobreak >nul

echo [2/4] Demarrage de MySQL XAMPP...
echo.
start "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone --console
timeout /t 5 /nobreak >nul

echo [3/4] Verification des services...
echo.
curl -s -o nul -w "Apache Status: %%{http_code}\n" http://localhost/
echo.

echo [4/4] Ouverture du site de test...
echo.
start "" "http://localhost/foreverbienetre"
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo   SERVICES DEMARRES
echo ========================================
echo.
echo   - Apache: http://localhost
echo   - MySQL: localhost:3306
echo   - WordPress: http://localhost/foreverbienetre
echo.
echo   PAGES FITTRACK A TESTER:
echo   - http://localhost/foreverbienetre/fittrack-pricing
echo   - http://localhost/foreverbienetre/fittrack-dashboard
echo   - http://localhost/foreverbienetre/fittrack-nutrition
echo   - http://localhost/foreverbienetre/fittrack-workouts
echo   - http://localhost/foreverbienetre/fittrack-progress
echo   - http://localhost/foreverbienetre/fittrack-goals
echo.
echo Appuyez sur une touche pour fermer...
pause >nul
