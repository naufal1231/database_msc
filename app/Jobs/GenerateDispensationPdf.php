<?php

namespace App\Jobs;

use App\Models\DispensationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class GenerateDispensationPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal.
     */
    public int $tries = 3;

    /**
     * Timeout dalam detik.
     */
    public int $timeout = 120;

    public function __construct(
        public readonly DispensationRequest $dispensation
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dispensation = $this->dispensation->fresh([
            'athlete',
            'competition',
        ]);

        // Periksa template tersedia
        $templatePath = $this->getTemplatePath();
        if (!$templatePath || !Storage::disk('local')->exists($templatePath)) {
            Log::error('GenerateDispensationPdf: Template tidak ditemukan.', [
                'dispensation_id' => $dispensation->id,
                'template_path'   => $templatePath,
            ]);
            // Jangan retry; tidak berguna sampai template diupload ulang
            $this->fail(new \RuntimeException('Template dispensasi tidak ditemukan.'));
            return;
        }

        try {
            // Salin template ke file sementara agar TemplateProcessor bisa memodifikasinya
            $tempDocx = tempnam(sys_get_temp_dir(), 'disp_') . '.docx';
            file_put_contents($tempDocx, Storage::disk('local')->get($templatePath));

            // Isi placeholder
            $processor = new TemplateProcessor($tempDocx);
            $processor->setValue('nama_atlet',    $dispensation->athlete->name);
            $processor->setValue('kelas',          $dispensation->school_class);
            $processor->setValue('no_absen',       $dispensation->school_absence_no);
            $processor->setValue('nama_kejuaraan', $dispensation->competition->name);
            $processor->setValue('tanggal',        $this->formatTanggalIndonesia(
                $dispensation->competition->start_date,
                $dispensation->competition->end_date
            ));
            $processor->setValue('tempat',         $dispensation->competition->location);

            // Simpan docx yang sudah diisi ke file temp lain
            $filledDocx = tempnam(sys_get_temp_dir(), 'disp_filled_') . '.docx';
            $processor->saveAs($filledDocx);

            // Konversi ke PDF menggunakan LibreOffice headless
            $pdfDir = sys_get_temp_dir();
            $soffice = $this->getLibreOfficePath();

            $escapedDocx = escapeshellarg($filledDocx);
            $escapedDir  = escapeshellarg($pdfDir);
            $command     = "{$soffice} --headless --convert-to pdf {$escapedDocx} --outdir {$escapedDir} 2>&1";

            exec($command, $output, $exitCode);

            if ($exitCode !== 0) {
                throw new \RuntimeException(
                    'LibreOffice gagal mengkonversi: ' . implode("\n", $output)
                );
            }

            $pdfTemp = $pdfDir . DIRECTORY_SEPARATOR . pathinfo($filledDocx, PATHINFO_FILENAME) . '.pdf';

            if (!file_exists($pdfTemp)) {
                throw new \RuntimeException('File PDF tidak ditemukan setelah konversi LibreOffice.');
            }

            // Simpan ke storage non-publik
            $safeName   = preg_replace('/[^A-Za-z0-9_\-]/', '_', $dispensation->athlete->name);
            $storagePath = "dispensation/pdf/{$dispensation->id}_{$safeName}.pdf";
            Storage::disk('local')->put($storagePath, file_get_contents($pdfTemp));

            // Update record
            $dispensation->update(['pdf_path' => $storagePath]);

            Log::info('GenerateDispensationPdf: PDF berhasil dibuat.', [
                'dispensation_id' => $dispensation->id,
                'pdf_path'        => $storagePath,
            ]);

        } catch (\Throwable $e) {
            Log::error('GenerateDispensationPdf: Gagal membuat PDF.', [
                'dispensation_id' => $dispensation->id,
                'error'           => $e->getMessage(),
                'trace'           => $e->getTraceAsString(),
            ]);
            throw $e; // Re-throw agar job bisa di-retry
        } finally {
            // Bersihkan file sementara
            @unlink($tempDocx ?? '');
            @unlink($filledDocx ?? '');
            @unlink($pdfTemp ?? '');
        }
    }

    /**
     * Mengambil path template dari settings.json.
     */
    protected function getTemplatePath(): ?string
    {
        $settingsFile = storage_path('app/settings.json');
        if (!file_exists($settingsFile)) {
            return null;
        }
        $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
        return $settings['dispensation_template_path'] ?? null;
    }

    /**
     * Path ke LibreOffice, bisa dikonfigurasi via .env LIBREOFFICE_PATH.
     */
    protected function getLibreOfficePath(): string
    {
        $path = env('LIBREOFFICE_PATH');
        if ($path) {
            return escapeshellarg($path);
        }

        // Windows XAMPP / default installation paths
        $windowsPaths = [
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe',
        ];
        foreach ($windowsPaths as $wp) {
            if (file_exists($wp)) {
                return escapeshellarg($wp);
            }
        }

        // Linux / server
        return 'soffice';
    }

    /**
     * Format tanggal kompetisi dalam bahasa Indonesia.
     * Contoh: "19 Oktober 2026" atau "19-21 Oktober 2026"
     */
    protected function formatTanggalIndonesia($startDate, $endDate): string
    {
        $bulanId = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        $start = \Carbon\Carbon::parse($startDate);
        $end   = \Carbon\Carbon::parse($endDate);

        if ($start->equalTo($end)) {
            return $start->day . ' ' . $bulanId[$start->month] . ' ' . $start->year;
        }

        if ($start->month === $end->month && $start->year === $end->year) {
            return $start->day . '-' . $end->day . ' ' . $bulanId[$start->month] . ' ' . $start->year;
        }

        return $start->day . ' ' . $bulanId[$start->month] . ' ' . $start->year
             . ' – ' . $end->day . ' ' . $bulanId[$end->month] . ' ' . $end->year;
    }

    /**
     * Callback ketika job gagal setelah semua percobaan.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateDispensationPdf: Job gagal permanen.', [
            'dispensation_id' => $this->dispensation->id,
            'error'           => $exception->getMessage(),
        ]);
    }
}
