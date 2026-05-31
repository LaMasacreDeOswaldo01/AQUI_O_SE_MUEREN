# Ejecuta este script desde PowerShell dentro de c:\xampp\htdocs\AQUI_O_SE_MUEREN-main

Set-Location "c:\xampp\htdocs\AQUI_O_SE_MUEREN-main"

if (-not (Test-Path .git)) {
    git init
}

git add .
git commit -m "Fix routes and allow login GET/POST" --allow-empty

git remote remove origin -ErrorAction SilentlyContinue

git remote add origin https://github.com/LaMasacreDeOswaldo01/AQUI_O_SE_MUEREN.git

git branch -M main

git push -u origin main
