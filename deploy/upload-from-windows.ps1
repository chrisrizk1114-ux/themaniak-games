# Package project for upload to themaniak.online (run on your PC)
# Creates deploy/themaniak-upload.zip — upload & extract on server, then run deploy/deploy.sh

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$OutZip = Join-Path $env:USERPROFILE "Desktop\themaniak-upload.zip"

if (Test-Path $OutZip) { Remove-Item $OutZip -Force }

$Exclude = @(
    "node_modules", "vendor", ".git", ".env", "storage\logs\*",
    "storage\framework\cache\data\*", "storage\framework\sessions\*",
    "storage\framework\views\*", "deploy\themaniak-upload.zip"
)

Write-Host "==> Zipping $Root (includes vendor for GoDaddy)"
Push-Location $Root
$items = Get-ChildItem -Force | Where-Object { $_.Name -notin @("node_modules", ".git", ".env") }
Compress-Archive -Path $items.FullName -DestinationPath $OutZip -Force
Pop-Location

Write-Host "==> Created: $OutZip"
Write-Host "Next:"
Write-Host "  1. Upload zip to server (cPanel File Manager or SFTP)"
Write-Host "  2. Extract to ~/themaniak-app"
Write-Host "  3. Follow deploy/cpanel-setup.txt or run deploy/deploy.sh on VPS"
