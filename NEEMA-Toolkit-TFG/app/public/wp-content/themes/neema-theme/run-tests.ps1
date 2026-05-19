#!/usr/bin/env pwsh
# Wrapper for PowerShell to run PHPUnit using the installed PHP binary
$php = 'C:\Users\david\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe'
$script = Join-Path $PSScriptRoot 'vendor\phpunit\phpunit\phpunit'
& $php $script --testsuite Unit @Args
