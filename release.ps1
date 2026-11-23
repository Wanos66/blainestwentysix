# ============================================================
# Blaines TwentySix — WordPress Theme Release Builder
# ============================================================
#
# Run from the blainestwentysix theme directory.
#
# Creates:
#   _release/blainestwentysix/
#   _release/blainestwentysix-X.Y.Z.zip
#
# The ZIP can be used for both Staging and Live.
#
# ============================================================

$ErrorActionPreference = "Stop"

$ThemeName = "blainestwentysix"
$Source = (Get-Location).Path

# ------------------------------------------------------------
# 1. Locate the Local site root
# ------------------------------------------------------------

# Work back from:
# ...\app\public\wp-content\themes\blainestwentysix
#
# to:
# ...\blaines-2026

$ThemesDirectory = Split-Path $Source -Parent
$WpContentDirectory = Split-Path $ThemesDirectory -Parent
$PublicDirectory = Split-Path $WpContentDirectory -Parent
$AppDirectory = Split-Path $PublicDirectory -Parent
$SiteRoot = Split-Path $AppDirectory -Parent

# ------------------------------------------------------------
# 2. Read the theme version from style.css
# ------------------------------------------------------------

$ThemeStylesheet = Join-Path $Source "style.css"

if (-not (Test-Path $ThemeStylesheet)) {
    throw "Theme style.css not found: $ThemeStylesheet"
}

$VersionMatch = Select-String `
    -Path $ThemeStylesheet `
    -Pattern '^\s*\*?\s*Version:\s*([0-9]+\.[0-9]+\.[0-9]+(?:\.[0-9]+)?)' |
        Select-Object -First 1

if (-not $VersionMatch) {
    throw "Unable to determine theme version from style.css"
}

$ThemeVersion = $VersionMatch.Matches[0].Groups[1].Value

Write-Host ""
Write-Host "Preparing $ThemeName version $ThemeVersion"

# ------------------------------------------------------------
# 3. Configure release paths
# ------------------------------------------------------------

$ReleaseRoot = Join-Path $SiteRoot "_release"
$ReleaseTheme = Join-Path $ReleaseRoot $ThemeName
$ZipFile = Join-Path $ReleaseRoot "$ThemeName-$ThemeVersion.zip"

# ------------------------------------------------------------
# 4. Prepare a clean release directory
# ------------------------------------------------------------

if (Test-Path $ReleaseRoot) {
    Remove-Item $ReleaseRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $ReleaseTheme -Force |
        Out-Null

# ------------------------------------------------------------
# 5. Copy production theme files
# ------------------------------------------------------------

# Exclude development source files, Git, dependencies,
# configuration files and other non-production material.

robocopy $Source $ReleaseTheme /E `
    /XD `
        node_modules `
        src `
        .git `
        .github `
        .idea `
        .vscode `
        tests `
        coverage `
    /XF `
        release.ps1 `
        package.json `
        package-lock.json `
        vite.config.js `
        postcss.config.js `
        tailwind.config.js `
        .gitignore `
        .gitattributes `
        .editorconfig `
        "*.map" `
        "*.log" `
        "README*.md"

# Robocopy exit codes 0–7 indicate successful copy outcomes.

if ($LASTEXITCODE -ge 8) {
    throw "Theme copy failed. Robocopy exit code: $LASTEXITCODE"
}

# ------------------------------------------------------------
# 6. Verify required WordPress theme files
# ------------------------------------------------------------

$RequiredFiles = @(
    "style.css",
    "functions.php",
    "index.php"
)

foreach ($File in $RequiredFiles) {
    $FilePath = Join-Path $ReleaseTheme $File

    if (-not (Test-Path $FilePath -PathType Leaf)) {
        throw "Required theme file missing: $File"
    }
}

$CompiledCSS = Join-Path $ReleaseTheme "assets\css\style.css"

if (-not (Test-Path $CompiledCSS -PathType Leaf)) {
    throw "Compiled stylesheet missing: assets/css/style.css"
}


# ------------------------------------------------------------
# 7. Create the version-numbered WordPress theme ZIP
# ------------------------------------------------------------

# Use bsdtar to create ZIP entries with forward-slash paths,
# ensuring compatibility with WordPress.

Push-Location $ReleaseRoot

try {
    & tar.exe -a -cf $ZipFile $ThemeName

    if ($LASTEXITCODE -ne 0) {
        throw "ZIP creation failed. tar exit code: $LASTEXITCODE"
    }
}
finally {
    Pop-Location
}


# ------------------------------------------------------------
# 8. Verify the ZIP contents
# ------------------------------------------------------------

Add-Type -AssemblyName System.IO.Compression

$Archive = [System.IO.Compression.ZipFile]::OpenRead($ZipFile)

try {
    $Entries = @($Archive.Entries.FullName)

    $RequiredZipEntry = "$ThemeName/style.css"

    if ($RequiredZipEntry -notin $Entries) {
        throw "ZIP structure invalid: $RequiredZipEntry not found"
    }
}
finally {
    $Archive.Dispose()
}

# ------------------------------------------------------------
# 9. Release summary
# ------------------------------------------------------------

$ZipInfo = Get-Item $ZipFile
$ZipSizeMB = [math]::Round($ZipInfo.Length / 1MB, 2)

Write-Host ""
Write-Host "============================================"
Write-Host " RELEASE CREATED SUCCESSFULLY"
Write-Host "============================================"
Write-Host ""
Write-Host " Theme:   $ThemeName"
Write-Host " Version: $ThemeVersion"
Write-Host " ZIP:     $ZipFile"
Write-Host " Size:    $ZipSizeMB MB"
Write-Host ""
Write-Host " Ready for Staging validation."
Write-Host ""
