@echo off
REM Create directory structure
mkdir assets\css\vendor
mkdir assets\css\modules
mkdir assets\js\vendor
mkdir assets\js\modules

REM Move all CSS files
for /R %%f in (*.css) do (
    if not "%%f"=="assets\css\main.css" (
        move "%%f" "assets\css\modules\"
    )
)

REM Move all JavaScript files
for /R %%f in (*.js) do (
    if not "%%f"=="assets\js\main.js" (
        move "%%f" "assets\js\modules\"
    )
)

echo Asset organization complete!
