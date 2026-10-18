<?php

namespace App\Services;

use App\Helpers\TimeHelper;
use App\Models\CompetitionEntry;

class CompetitionPointService
{
    // Performance category labels — ubah hanya di sini bila diperlukan
    const LABEL_BAIK  = 'Baik';
    const LABEL_CUKUP = 'Cukup';
    const LABEL_GAGAL = 'Gagal';

    /**
     * Nilai default konfigurasi poin dan ambang performa.
     */
    public static function defaults(): array
    {
        return [
            'poin_lebih_cepat'     => 10,
            'poin_sama'            => 5,
            'poin_lebih_lambat'    => 0,
            'poin_debut'           => 10,
            'performance_good_min' => 70,
            'performance_fair_min' => 50,
        ];
    }

    /**
     * Gabungkan settings user dengan defaults.
     * Key yang tidak ada di settings akan diisi dari defaults.
     */
    public static function resolveSettings(array $settings): array
    {
        return array_merge(self::defaults(), array_intersect_key($settings, self::defaults()));
    }

    /**
     * Hitung poin untuk satu entry.
     *
     * @param  CompetitionEntry  $entry  Harus sudah load relasi 'result'.
     * @param  array             $cfg    Konfigurasi dari resolveSettings().
     *
     * @return array {
     *   limit_cs:     int|null,
     *   final_cs:     int|null,
     *   selisih_cs:   int|null,             // nilai mutlak dalam centiseconds
     *   direction:    'faster'|'slower'|'equal'|null,
     *   points:       int|null,             // null = belum bertanding (tidak dihitung)
     *   is_counted:   bool,
     *   is_debut:     bool,
     *   status_label: string|null,          // 'DNS', 'DQ', 'Debut', atau null
     * }
     */
    public static function calculateEntry(CompetitionEntry $entry, array $cfg): array
    {
        // Belum ada result => tidak dihitung sama sekali
        if (! $entry->result) {
            return [
                'limit_cs'     => null,
                'final_cs'     => null,
                'selisih_cs'   => null,
                'direction'    => null,
                'points'       => null,
                'is_counted'   => false,
                'is_debut'     => false,
                'status_label' => null,
            ];
        }

        $result = $entry->result;

        // DNS atau DQ => 0 poin, masuk hitungan, tanpa selisih
        if (in_array($result->status, ['DNS', 'DQ'], true)) {
            return [
                'limit_cs'     => null,
                'final_cs'     => null,
                'selisih_cs'   => null,
                'direction'    => null,
                'points'       => 0,
                'is_counted'   => true,
                'is_debut'     => false,
                'status_label' => $result->status,
            ];
        }

        // Hasil finish — bandingkan waktu
        $finalCs = TimeHelper::toCentiseconds($result->time_result);
        $limitCs = (isset($entry->seed_time) && trim((string) $entry->seed_time) !== '')
            ? TimeHelper::toCentiseconds($entry->seed_time)
            : null;

        // Debut: tidak ada seed_time => otomatis jadi PB pertama
        if ($limitCs === null) {
            return [
                'limit_cs'     => null,
                'final_cs'     => $finalCs,
                'selisih_cs'   => null,
                'direction'    => null,
                'points'       => (int) $cfg['poin_debut'],
                'is_counted'   => true,
                'is_debut'     => true,
                'status_label' => 'Debut',
            ];
        }

        // Bandingkan final vs limit
        if ($finalCs < $limitCs) {
            $direction = 'faster';
            $points    = (int) $cfg['poin_lebih_cepat'];
        } elseif ($finalCs === $limitCs) {
            $direction = 'equal';
            $points    = (int) $cfg['poin_sama'];
        } else {
            $direction = 'slower';
            $points    = (int) $cfg['poin_lebih_lambat'];
        }

        $selisihCs = abs($finalCs - $limitCs);

        return [
            'limit_cs'     => $limitCs,
            'final_cs'     => $finalCs,
            'selisih_cs'   => $selisihCs,
            'direction'    => $direction,
            'points'       => $points,
            'is_counted'   => true,
            'is_debut'     => false,
            'status_label' => null,
        ];
    }

    /**
     * Rangkum performa satu atlet dari sekumpulan entry-nya.
     *
     * @param  iterable  $entries  Koleksi CompetitionEntry dengan relasi 'result' sudah ter-load.
     * @param  array     $cfg      Konfigurasi dari resolveSettings().
     *
     * @return array {
     *   total_points:    int,
     *   counted_entries: int,
     *   max_points:      int,
     *   percentage:      float|null,   // null bila belum ada entry yang dihitung
     *   category:        string|null,  // 'Baik'|'Cukup'|'Gagal'|null
     *   entry_results:   array,        // hasil calculateEntry per entry (dengan key 'entry')
     * }
     */
    public static function summarizeAthlete(iterable $entries, array $cfg): array
    {
        $totalPoints    = 0;
        $countedEntries = 0;
        $entryResults   = [];

        foreach ($entries as $entry) {
            $calc           = self::calculateEntry($entry, $cfg);
            $calc['entry']  = $entry;
            $entryResults[] = $calc;

            if ($calc['is_counted']) {
                $totalPoints    += $calc['points'];
                $countedEntries++;
            }
        }

        if ($countedEntries === 0) {
            return [
                'total_points'    => 0,
                'counted_entries' => 0,
                'max_points'      => 0,
                'percentage'      => null,
                'category'        => null,
                'entry_results'   => $entryResults,
            ];
        }

        $poinMaksimalPerNomor = (int) ($cfg['poin_lebih_cepat'] ?? 10);
        $maxPoints  = $countedEntries * $poinMaksimalPerNomor;
        $percentage = $maxPoints > 0 ? ($totalPoints / $maxPoints) * 100 : 0;

        return [
            'total_points'    => $totalPoints,
            'counted_entries' => $countedEntries,
            'max_points'      => $maxPoints,
            'percentage'      => round($percentage, 1),
            'category'        => self::getCategory($percentage, $cfg),
            'entry_results'   => $entryResults,
        ];
    }

    /**
     * Tentukan kategori performa berdasarkan persentase.
     */
    public static function getCategory(float $percentage, array $cfg): string
    {
        if ($percentage >= (float) $cfg['performance_good_min']) {
            return self::LABEL_BAIK;
        }
        if ($percentage >= (float) $cfg['performance_fair_min']) {
            return self::LABEL_CUKUP;
        }
        return self::LABEL_GAGAL;
    }

    /**
     * Bangun data tampilan "Per Atlet" untuk satu competition.
     *
     * Syarat: $competition sudah eager-load:
     *   events -> entries -> athlete
     *   events -> entries -> result
     *
     * @param  \App\Models\Competition  $competition
     * @param  array                    $cfg          Konfigurasi dari resolveSettings().
     *
     * @return array  Terindeks athlete_id, diurutkan ascending berdasarkan nama atlet.
     */
    public static function buildAthleteView($competition, array $cfg): array
    {
        $athleteEntries = [];

        foreach ($competition->events as $event) {
            foreach ($event->entries as $entry) {
                $athleteId = $entry->athlete_id;

                if (! isset($athleteEntries[$athleteId])) {
                    $athleteEntries[$athleteId] = [
                        'athlete' => $entry->athlete,
                        'entries' => [],
                    ];
                }

                $entry->setRelation('competitionEvent', $event);
                $athleteEntries[$athleteId]['entries'][] = $entry;
            }
        }

        $result = [];
        foreach ($athleteEntries as $athleteId => $data) {
            $summary            = self::summarizeAthlete($data['entries'], $cfg);
            $result[$athleteId] = array_merge($data, $summary);
        }

        uasort($result, fn ($a, $b) => strcmp($a['athlete']->name, $b['athlete']->name));

        return $result;
    }

    /**
     * Format selisih centiseconds menjadi string bertanda untuk tampilan.
     * Contoh: '14.28.64' faster => '&#9660; -00.01.71'
     *         '14.28.64' slower => '&#9650; +00.00.45'
     *         equal             => '00.00.00'
     */
    public static function formatSelisih(int $selisihCs, string $direction): string
    {
        if ($direction === 'equal' || $selisihCs === 0) {
            return '00.00.00';
        }

        $formatted = TimeHelper::fromCentiseconds($selisihCs);

        if ($direction === 'faster') {
            return '-' . $formatted;
        }

        return '+' . $formatted;
    }
}
