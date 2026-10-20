<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->role === 'user';
    }

    public function rules(): array
    {
        return [
            'competition_id'    => ['required', 'exists:competitions,id'],
            'athlete_id'        => [
                'required',
                'exists:athletes,id',
                // Validasi kepemilikan: atlet harus milik orang tua yang login
                function ($attribute, $value, $fail) {
                    $athlete = \App\Models\Athlete::find($value);
                    if (!$athlete || $athlete->parent_user_id !== auth()->id()) {
                        $fail('Atlet yang dipilih bukan milik akun Anda.');
                    }
                },
            ],
            'school_class'      => ['required', 'string', 'max:100'],
            'school_absence_no' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'competition_id.required' => 'Kompetisi harus dipilih.',
            'competition_id.exists'   => 'Kompetisi yang dipilih tidak valid.',
            'athlete_id.required'     => 'Atlet harus dipilih.',
            'school_class.required'   => 'Kelas sekolah harus diisi.',
            'school_absence_no.required' => 'Nomor absen sekolah harus diisi.',
        ];
    }
}
