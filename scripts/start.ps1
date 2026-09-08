param([string]$PostgresContainer = 'postgres')

$ErrorActionPreference = 'Stop'
function Invoke-Checked {
    param([string]$Command, [string[]]$Arguments)
    & $Command @Arguments
    if ($LASTEXITCODE -ne 0) { throw "$Command failed with exit code $LASTEXITCODE." }
}

Push-Location (Split-Path -Parent $PSScriptRoot)
try {
    if (-not (Test-Path -LiteralPath '.env')) { Copy-Item -LiteralPath '.env.example' -Destination '.env' }
    $container = (docker inspect $PostgresContainer | ConvertFrom-Json)[0]
    if (-not $container.State.Running) { throw "Start PostgreSQL container '$PostgresContainer' first." }
    $pgUser = ($container.Config.Env | Where-Object { $_ -like 'POSTGRES_USER=*' }) -replace '^POSTGRES_USER=', ''
    if (-not $pgUser) { $pgUser = 'postgres' }
    $sql = @'
SELECT 'CREATE ROLE jtsid_app LOGIN PASSWORD ''JtsLocalDb2026!'''
WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'jtsid_app')\gexec
SELECT 'CREATE DATABASE jtsid OWNER jtsid_app'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'jtsid')\gexec
SELECT 'CREATE DATABASE jtsid_test OWNER jtsid_app'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'jtsid_test')\gexec
'@
    $sql | docker exec -i $PostgresContainer psql -v ON_ERROR_STOP=1 -U $pgUser -d postgres
    if ($LASTEXITCODE -ne 0) { throw 'Database setup failed.' }
    Invoke-Checked docker @('compose', 'build', 'app')
    Invoke-Checked docker @('compose', 'run', '--rm', 'app', 'composer', 'install', '--no-interaction', '--prefer-dist')
    $envText = Get-Content -LiteralPath '.env' -Raw
    if ($envText -notmatch '(?m)^APP_KEY=base64:.+') {
        Invoke-Checked docker @('compose', 'run', '--rm', 'app', 'php', 'artisan', 'key:generate')
    }
    Invoke-Checked docker @('compose', 'run', '--rm', 'app', 'php', 'artisan', 'migrate', '--seed', '--force')
    Invoke-Checked docker @('compose', 'run', '--rm', 'app', 'php', 'artisan', 'filament:assets')
    Invoke-Checked docker @('compose', 'run', '--rm', 'app', 'php', 'artisan', 'storage:link', '--force')
    Invoke-Checked npm.cmd @('ci')
    Invoke-Checked npm.cmd @('run', 'build')
    Invoke-Checked docker @('compose', 'up', '-d', 'app')
    $ready = $false
    for ($attempt = 0; $attempt -lt 20; $attempt++) {
        try {
            $response = Invoke-WebRequest 'http://localhost:8000/up' -TimeoutSec 5
            if ($response.StatusCode -eq 200) { $ready = $true; break }
        } catch {
            Start-Sleep -Milliseconds 500
        }
    }
    if (-not $ready) { throw 'The application did not become ready. Check docker compose logs app.' }
    Write-Host 'Public: http://localhost:8000 | Admin: http://localhost:8000/admin'
} finally {
    Pop-Location
}
