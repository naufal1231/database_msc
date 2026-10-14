<?php

namespace Tests\Unit;

use App\Helpers\TimeHelper;
use App\Models\CompetitionEntry;
use App\Models\Result;
use App\Services\CompetitionPointService;
use PHPUnit\Framework\TestCase;

class CompetitionPointServiceTest extends TestCase
{
    private array $cfg;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cfg = CompetitionPointService::resolveSettings([]);
    }

    private function makeEntry(
        ?string $seedTime,
        ?string $finalTime,
        string $resultStatus = 'finish'
    ): CompetitionEntry {
        $entry            = new CompetitionEntry();
        $entry->seed_time = $seedTime;

        if ($finalTime !== null) {
            $result              = new Result();
            $result->time_result = $finalTime;
            $result->status      = $resultStatus;
            $entry->setRelation('result', $result);
        } else {
            $entry->setRelation('result', null);
        }

        return $entry;
    }

    public function test_faster_than_limit_gives_10_points(): void
    {
        $entry = $this->makeEntry('04.28.64', '04.26.93');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertTrue($calc['is_counted']);
        $this->assertSame(10, $calc['points']);
        $this->assertSame('faster', $calc['direction']);
        $this->assertFalse($calc['is_debut']);
        $this->assertNull($calc['status_label']);
    }

    public function test_debut_no_seed_time_gives_poin_debut(): void
    {
        $entry = $this->makeEntry(null, '00.31.56');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertTrue($calc['is_counted']);
        $this->assertTrue($calc['is_debut']);
        $this->assertSame(10, $calc['points']);
        $this->assertSame('Debut', $calc['status_label']);
        $this->assertNull($calc['limit_cs']);
    }

    public function test_slower_than_limit_gives_0_points(): void
    {
        $entry = $this->makeEntry('00.57.94', '00.58.09');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertTrue($calc['is_counted']);
        $this->assertSame(0, $calc['points']);
        $this->assertSame('slower', $calc['direction']);
    }

    public function test_equal_to_limit_gives_5_points(): void
    {
        $entry = $this->makeEntry('00.57.94', '00.57.94');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertTrue($calc['is_counted']);
        $this->assertSame(5, $calc['points']);
        $this->assertSame('equal', $calc['direction']);
        $this->assertSame(0, $calc['selisih_cs']);
    }

    public function test_no_result_is_not_counted(): void
    {
        $entry = $this->makeEntry('00.57.94', null);
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertFalse($calc['is_counted']);
        $this->assertNull($calc['points']);
        $this->assertNull($calc['direction']);
    }

    public function test_dns_gives_0_points_counted(): void
    {
        $entry = $this->makeEntry('00.57.94', '00.00.00', 'DNS');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertTrue($calc['is_counted']);
        $this->assertSame(0, $calc['points']);
        $this->assertSame('DNS', $calc['status_label']);
        $this->assertNull($calc['selisih_cs']);
    }

    public function test_dq_gives_0_points_counted(): void
    {
        $entry = $this->makeEntry('00.57.94', '00.00.00', 'DQ');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertTrue($calc['is_counted']);
        $this->assertSame(0, $calc['points']);
        $this->assertSame('DQ', $calc['status_label']);
    }

    public function test_full_7_entry_scenario_from_specification(): void
    {
        $entries = [
            $this->makeEntry('04.28.64', '04.26.93'),
            $this->makeEntry(null,       '00.31.56'),
            $this->makeEntry('00.57.94', '00.58.09'),
            $this->makeEntry('02.07.11', '02.04.93'),
            $this->makeEntry('01.03.03', '01.03.65'),
            $this->makeEntry('00.26.18', '00.26.39'),
            $this->makeEntry('00.28.49', '00.29.58'),
        ];

        $summary = CompetitionPointService::summarizeAthlete($entries, $this->cfg);

        $this->assertSame(30, $summary['total_points']);
        $this->assertSame(7,  $summary['counted_entries']);
        $this->assertSame(70, $summary['max_points']);
        $this->assertEqualsWithDelta(42.9, $summary['percentage'], 0.1);
        $this->assertSame(CompetitionPointService::LABEL_GAGAL, $summary['category']);
    }

    public function test_category_baik_when_percentage_at_or_above_good_min(): void
    {
        $entries = array_map(
            fn () => $this->makeEntry('01.00.00', '00.59.00'),
            range(1, 7)
        );
        $summary = CompetitionPointService::summarizeAthlete($entries, $this->cfg);
        $this->assertSame(CompetitionPointService::LABEL_BAIK, $summary['category']);
        $this->assertSame(100.0, $summary['percentage']);
    }

    public function test_category_cukup_when_percentage_between_fair_and_good(): void
    {
        $faster = array_map(fn () => $this->makeEntry('01.00.00', '00.59.00'), range(1, 4));
        $slower = array_map(fn () => $this->makeEntry('01.00.00', '01.01.00'), range(1, 3));
        $summary = CompetitionPointService::summarizeAthlete(array_merge($faster, $slower), $this->cfg);
        $this->assertSame(CompetitionPointService::LABEL_CUKUP, $summary['category']);
    }

    public function test_category_gagal_when_percentage_below_fair_min(): void
    {
        $faster = array_map(fn () => $this->makeEntry('01.00.00', '00.59.00'), range(1, 3));
        $slower = array_map(fn () => $this->makeEntry('01.00.00', '01.01.00'), range(1, 7));
        $summary = CompetitionPointService::summarizeAthlete(array_merge($faster, $slower), $this->cfg);
        $this->assertSame(CompetitionPointService::LABEL_GAGAL, $summary['category']);
    }

    public function test_boundary_categories_via_getCategory(): void
    {
        $cfg = CompetitionPointService::resolveSettings([]);
        $this->assertSame(CompetitionPointService::LABEL_BAIK,  CompetitionPointService::getCategory(70.0, $cfg));
        $this->assertSame(CompetitionPointService::LABEL_CUKUP, CompetitionPointService::getCategory(69.9, $cfg));
        $this->assertSame(CompetitionPointService::LABEL_CUKUP, CompetitionPointService::getCategory(50.0, $cfg));
        $this->assertSame(CompetitionPointService::LABEL_GAGAL, CompetitionPointService::getCategory(49.9, $cfg));
    }

    public function test_no_counted_entries_returns_null_percentage_and_category(): void
    {
        $entries = [
            $this->makeEntry('01.00.00', null),
            $this->makeEntry('00.30.00', null),
        ];
        $summary = CompetitionPointService::summarizeAthlete($entries, $this->cfg);

        $this->assertSame(0, $summary['counted_entries']);
        $this->assertNull($summary['percentage']);
        $this->assertNull($summary['category']);
        $this->assertSame(0, $summary['total_points']);
    }

    public function test_selisih_cs_computed_correctly(): void
    {
        $entry = $this->makeEntry('04.28.64', '04.26.93');
        $calc  = CompetitionPointService::calculateEntry($entry, $this->cfg);

        $this->assertSame(26864, $calc['limit_cs']);
        $this->assertSame(26693, $calc['final_cs']);
        $this->assertSame(171, $calc['selisih_cs']);
    }

    public function test_format_selisih_faster(): void
    {
        $this->assertSame('▼ -00.01.71', CompetitionPointService::formatSelisih(171, 'faster'));
    }

    public function test_format_selisih_slower(): void
    {
        $this->assertSame('▲ +00.00.45', CompetitionPointService::formatSelisih(45, 'slower'));
    }

    public function test_format_selisih_equal(): void
    {
        $this->assertSame('00.00.00', CompetitionPointService::formatSelisih(0, 'equal'));
    }

    public function test_timehelper_toCentiseconds_accuracy(): void
    {
        $this->assertSame(26864, TimeHelper::toCentiseconds('04.28.64'));
        $this->assertSame(12711, TimeHelper::toCentiseconds('02.07.11'));
        $this->assertSame(5794,  TimeHelper::toCentiseconds('00.57.94'));
    }

    public function test_timehelper_fromCentiseconds_accuracy(): void
    {
        $this->assertSame('04.28.64', TimeHelper::fromCentiseconds(26864));
        $this->assertSame('00.01.71', TimeHelper::fromCentiseconds(171));
        $this->assertSame('00.00.45', TimeHelper::fromCentiseconds(45));
    }

    public function test_resolve_settings_uses_defaults_for_missing_keys(): void
    {
        $cfg = CompetitionPointService::resolveSettings(['performance_good_min' => 80]);
        $this->assertSame(80, $cfg['performance_good_min']);
        $this->assertSame(10, $cfg['poin_lebih_cepat']);
        $this->assertSame(5,  $cfg['poin_sama']);
    }

    public function test_custom_point_values_applied(): void
    {
        $cfg   = CompetitionPointService::resolveSettings(['poin_lebih_cepat' => 15]);
        $entry = $this->makeEntry('01.00.00', '00.59.00');
        $calc  = CompetitionPointService::calculateEntry($entry, $cfg);
        $this->assertSame(15, $calc['points']);
    }

    public function test_custom_debut_points_applied(): void
    {
        $cfg   = CompetitionPointService::resolveSettings(['poin_debut' => 8]);
        $entry = $this->makeEntry(null, '00.31.56');
        $calc  = CompetitionPointService::calculateEntry($entry, $cfg);
        $this->assertSame(8, $calc['points']);
    }

    public function test_mixed_dns_and_normal_entries(): void
    {
        $entries = [
            $this->makeEntry('01.00.00', '00.59.00'),
            $this->makeEntry('01.00.00', '00.00.00', 'DNS'),
            $this->makeEntry('00.30.00', '00.30.50'),
        ];
        $summary = CompetitionPointService::summarizeAthlete($entries, $this->cfg);

        $this->assertSame(10, $summary['total_points']);
        $this->assertSame(3,  $summary['counted_entries']);
        $this->assertSame(30, $summary['max_points']);
        $this->assertEqualsWithDelta(33.3, $summary['percentage'], 0.2);
        $this->assertSame(CompetitionPointService::LABEL_GAGAL, $summary['category']);
    }

    public function test_entry_without_result_does_not_affect_counted_entries(): void
    {
        $entries = [
            $this->makeEntry('01.00.00', '00.59.00'),
            $this->makeEntry('01.00.00', null),
        ];
        $summary = CompetitionPointService::summarizeAthlete($entries, $this->cfg);

        $this->assertSame(10, $summary['total_points']);
        $this->assertSame(1,  $summary['counted_entries']);
        $this->assertSame(10, $summary['max_points']);
        $this->assertSame(100.0, $summary['percentage']);
    }
}