<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PhpOffice\PhpWord\TemplateProcessor;

class UploadDispensationTemplateRequest extends FormRequest
{
    /**
     * Placeholder wajib yang harus ada di template.
     */
    public const REQUIRED_PLACEHOLDERS = [
        'nama_atlet',
        'kelas',
        'no_absen',
        'nama_kejuaraan',
        'tanggal',
        'tempat',
    ];

    public function authorize(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'super_admin']);
    }

    public function rules(): array
    {
        return [
            'template' => [
                'required',
                'file',
                'mimes:docx',
                'max:10240', // 10 MB
                function ($attribute, $value, $fail) {
                    $this->validatePlaceholders($value, $fail);
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'template.required' => 'File template harus dipilih.',
            'template.mimes'    => 'File harus berformat .docx.',
            'template.max'      => 'Ukuran file tidak boleh melebihi 10 MB.',
        ];
    }

    /**
     * Validasi bahwa file .docx mengandung semua placeholder wajib.
     */
    protected function validatePlaceholders($file, callable $fail): void
    {
        try {
            // Salin ke file sementara agar dapat dibaca TemplateProcessor
            $tempPath = $file->getRealPath();
            $processor = new TemplateProcessor($tempPath);

            $missing = [];
            foreach (self::REQUIRED_PLACEHOLDERS as $placeholder) {
                // TemplateProcessor menyimpan variabel; cek dengan getVariables()
                if (!in_array($placeholder, $processor->getVariables())) {
                    $missing[] = '${' . $placeholder . '}';
                }
            }

            if (!empty($missing)) {
                $fail(
                    'Template tidak valid. Placeholder berikut tidak ditemukan: '
                    . implode(', ', $missing)
                    . '. Pastikan semua placeholder wajib ada dalam dokumen.'
                );
            }
        } catch (\Throwable $e) {
            $fail('Gagal membaca file template. Pastikan file adalah dokumen .docx yang valid.');
        }
    }
}
