# ==============================================================================
# SCRIPT TESTING KESELURUHAN APLIKASI MSC (LARAVEL 12)
# Tiga Lapis: Smoke Test -> Feature & Business Workflow -> Unified Report
# ==============================================================================

param(
    [switch]$Detailed,
    [switch]$SmokeOnly,
    [switch]$FeatureOnly
)

$Host.UI.RawUI.WindowTitle = "MSC Testing Suite Runner"
$startTime = Get-Date

Write-Host ""
Write-Host "=================================================================" -ForegroundColor Cyan
Write-Host "   MARABUNTA SWIMMING CLUB (MSC) - AUTOMATED TEST RUNNER       " -ForegroundColor Yellow
Write-Host "=================================================================" -ForegroundColor Cyan
Write-Host "[INFO] Lingkungan: Isolated In-Memory SQLite" -ForegroundColor Gray
Write-Host "[INFO] Database asli MySQL: 100% Aman & Tidak Tersentuh" -ForegroundColor Gray
Write-Host "[INFO] Dimulai pada: $($startTime.ToString('yyyy-MM-dd HH:mm:ss'))" -ForegroundColor Gray
Write-Host ""

# ------------------------------------------------------------------------------
# STEP 0: Bersihkan Cache untuk memastikan environment bersih
# ------------------------------------------------------------------------------
Write-Host ">>> Langkah 0: Membersihkan Cache Aplikasi..." -ForegroundColor Cyan
php artisan config:clear | Out-Null
php artisan route:clear | Out-Null
php artisan view:clear | Out-Null
Write-Host "    [OK] Cache bersih." -ForegroundColor Green
Write-Host ""

$testSuites = @()

if (-not $FeatureOnly) {
    $testSuites += [PSCustomObject]@{
        Layer = "Lapis 1 (Smoke Test)"
        Name = "Pemeriksaan Kesehatan Halaman & Rute"
        File = "tests/Feature/SmokeTest.php"
    }
}

if (-not $SmokeOnly) {
    $testSuites += [PSCustomObject]@{
        Layer = "Lapis 2 (Workflow)"
        Name = "Otentikasi & Persetujuan Orang Tua"
        File = "tests/Feature/AuthAndApprovalTest.php"
    }
    $testSuites += [PSCustomObject]@{
        Layer = "Lapis 2 (Workflow)"
        Name = "Manajemen Atlet & Riwayat Kelas"
        File = "tests/Feature/AthleteWorkflowTest.php"
    }
    $testSuites += [PSCustomObject]@{
        Layer = "Lapis 2 (Workflow)"
        Name = "Kompetisi, Nomor Lomba & Hasil"
        File = "tests/Feature/CompetitionWorkflowTest.php"
    }
    $testSuites += [PSCustomObject]@{
        Layer = "Lapis 2 (Workflow)"
        Name = "Presensi / Absensi & Rekap"
        File = "tests/Feature/AttendanceWorkflowTest.php"
    }
    $testSuites += [PSCustomObject]@{
        Layer = "Lapis 2 (Workflow)"
        Name = "Matriks Keamanan & Hak Akses Role"
        File = "tests/Feature/RoleSecurityTest.php"
    }
}

$results = @()
$totalPassed = 0
$totalFailed = 0

Write-Host ">>> Menjalankan Pengujian..." -ForegroundColor Cyan
Write-Host "-----------------------------------------------------------------" -ForegroundColor Gray

foreach ($suite in $testSuites) {
    $suiteStart = Get-Date
    Write-Host -NoNewline "[$($suite.Layer)] $($suite.Name)... "
    
    $output = php vendor/bin/phpunit $suite.File 2>&1
    $suiteDuration = ((Get-Date) - $suiteStart).TotalSeconds
    
    $isOk = ($LASTEXITCODE -eq 0)
    
    if ($isOk) {
        Write-Host "PASSED ($([math]::Round($suiteDuration, 2))s)" -ForegroundColor Green
        $totalPassed++
        $statusStr = "PASSED"
    } else {
        Write-Host "FAILED ($([math]::Round($suiteDuration, 2))s)" -ForegroundColor Red
        $totalFailed++
        $statusStr = "FAILED"
        if ($Detailed) {
            Write-Host ($output -join "`n") -ForegroundColor DarkRed
        }
    }
    
    $results += [PSCustomObject]@{
        Layer = $suite.Layer
        Modul = $suite.Name
        File = $suite.File
        Status = $statusStr
        Duration = "$([math]::Round($suiteDuration, 2))s"
    }
}

# ------------------------------------------------------------------------------
# STEP AKHIR: Menjalankan Keseluruhan Suite (Termasuk Unit & Auth Breeze)
# ------------------------------------------------------------------------------
Write-Host ""
Write-Host ">>> Menjalankan Verifikasi Akhir (Seluruh Test Suite PHPUnit)..." -ForegroundColor Cyan
$allStart = Get-Date
$allOutput = php vendor/bin/phpunit 2>&1
$allExit = $LASTEXITCODE
$allDuration = ((Get-Date) - $allStart).TotalSeconds

# Parse summary line from PHPUnit output
$summaryLine = ($allOutput | Where-Object { $_ -match "Tests: \d+" } | Select-Object -Last 1)
if (-not $summaryLine) {
    $summaryLine = ($allOutput | Where-Object { $_ -match "OK \(" } | Select-Object -Last 1)
}

Write-Host ""
Write-Host "=================================================================" -ForegroundColor Cyan
Write-Host "                     RINGKASAN HASIL PENGUJIAN                   " -ForegroundColor Yellow
Write-Host "=================================================================" -ForegroundColor Cyan

$results | Format-Table -AutoSize -Property Layer, Modul, Status, Duration

$totalTime = ((Get-Date) - $startTime).TotalSeconds
Write-Host "-----------------------------------------------------------------" -ForegroundColor Gray
Write-Host "Total Modul Diuji  : $($testSuites.Count)" -ForegroundColor White
Write-Host "Modul Berhasil     : $totalPassed" -ForegroundColor Green
if ($totalFailed -gt 0) {
    Write-Host "Modul Gagal        : $totalFailed" -ForegroundColor Red
} else {
    Write-Host "Modul Gagal        : 0" -ForegroundColor Green
}
Write-Host "Waktu Eksekusi     : $([math]::Round($totalTime, 2)) detik" -ForegroundColor Gray
if ($summaryLine) {
    Write-Host "Detail PHPUnit     : $summaryLine" -ForegroundColor Yellow
}
Write-Host "=================================================================" -ForegroundColor Cyan

# Simpan laporan ke file
$reportDir = "tests/reports"
if (-not (Test-Path $reportDir)) {
    New-Item -ItemType Directory -Path $reportDir | Out-Null
}

$reportPath = "$reportDir/latest_test_report.txt"
$reportContent = @"
=================================================================
LAPORAN TESTING APLIKASI MSC (MARABUNTA SWIMMING CLUB)
Tanggal Eksekusi: $($startTime.ToString('yyyy-MM-dd HH:mm:ss'))
Durasi Total     : $([math]::Round($totalTime, 2)) detik
=================================================================

HASIL PER MODUL:
$($results | Format-Table -AutoSize -Property Layer, Modul, Status, Duration | Out-String)

DETAIL PHPUNIT:
$summaryLine

STATUS KESELURUHAN: $(if ($totalFailed -eq 0) { "SEMUA PENGUJIAN LOLOS (100% SUKSES)" } else { "ADA MODUL YANG GAGAL" })
=================================================================
"@

$reportContent | Out-File -FilePath $reportPath -Encoding utf8
Write-Host "[LAPORAN TERSIMPAN] $reportPath" -ForegroundColor Gray
Write-Host ""
