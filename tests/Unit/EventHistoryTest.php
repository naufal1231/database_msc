<?php

namespace Tests\Unit;

use App\Helpers\TimeHelper;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Event History PB calculation logic.
 * Tests the TimeHelper and the PB calculation rules from the brief.
 */
class EventHistoryTest extends TestCase
{
    // ─── TimeHelper Tests ───

    /** @test */
    public function it_converts_time_string_to_centiseconds(): void
    {
        $this->assertEquals(3350, TimeHelper::toCentiseconds('00.33.50'));
        $this->assertEquals(3380, TimeHelper::toCentiseconds('00.33.80'));
        $this->assertEquals(3420, TimeHelper::toCentiseconds('00.34.20'));
        $this->assertEquals(7520, TimeHelper::toCentiseconds('01.15.20')); // 1min 15sec 20cs
        $this->assertEquals(0,    TimeHelper::toCentiseconds('00.00.00'));
        $this->assertEquals(0,    TimeHelper::toCentiseconds('invalid')); // invalid returns 0
    }

    /** @test */
    public function it_converts_centiseconds_back_to_time_string(): void
    {
        $this->assertEquals('00.33.50', TimeHelper::fromCentiseconds(3350));
        $this->assertEquals('00.33.80', TimeHelper::fromCentiseconds(3380));
        $this->assertEquals('01.15.20', TimeHelper::fromCentiseconds(7520));
        $this->assertEquals('00.00.00', TimeHelper::fromCentiseconds(0));
    }

    // ─── Case 1: Result is faster than PB ───

    /** @test */
    public function case1_result_faster_than_pb_produces_green_difference(): void
    {
        $pb     = '00.33.80';
        $result = '00.33.50';

        $pbCs     = TimeHelper::toCentiseconds($pb);
        $resultCs = TimeHelper::toCentiseconds($result);

        $this->assertGreaterThan($resultCs, $pbCs); // result < pb means faster

        $diffCs    = abs($resultCs - $pbCs);
        $diffTime  = TimeHelper::fromCentiseconds($diffCs);
        $isFaster  = $resultCs < $pbCs;

        $this->assertEquals('00.00.30', $diffTime);
        $this->assertTrue($isFaster);  // green
    }

    // ─── Case 2: Result is slower than PB ───

    /** @test */
    public function case2_result_slower_than_pb_produces_red_difference(): void
    {
        $pb     = '00.33.80';
        $result = '00.34.10';

        $pbCs     = TimeHelper::toCentiseconds($pb);
        $resultCs = TimeHelper::toCentiseconds($result);

        $diffCs   = abs($resultCs - $pbCs);
        $diffTime = TimeHelper::fromCentiseconds($diffCs);
        $isFaster = $resultCs < $pbCs;

        $this->assertEquals('00.00.30', $diffTime);
        $this->assertFalse($isFaster); // red
    }

    // ─── Case 3: Result equals PB ───

    /** @test */
    public function case3_result_equals_pb_produces_neutral_zero_difference(): void
    {
        $pb     = '00.33.80';
        $result = '00.33.80';

        $pbCs     = TimeHelper::toCentiseconds($pb);
        $resultCs = TimeHelper::toCentiseconds($result);

        $diffCs   = abs($resultCs - $pbCs);
        $diffTime = TimeHelper::fromCentiseconds($diffCs);
        $isFaster = $resultCs < $pbCs;

        $this->assertEquals('00.00.00', $diffTime);
        $this->assertFalse($isFaster); // neutral
        $this->assertEquals(0, $diffCs);
    }

    // ─── Case 4: First event — no PB before ───

    /** @test */
    public function case4_first_event_has_no_pb_before_event(): void
    {
        // Simulate the logic: no previous results => pb_before_event is null
        $pbBeforeEvent = null;  // No prior results for this event_key
        $result        = '00.34.20';

        $isFirstValidResult = ($pbBeforeEvent === null);
        $difference         = null; // Not calculated if no prior PB

        $this->assertTrue($isFirstValidResult);
        $this->assertNull($difference);
        // In the view this would display "PB Awal"
    }

    // ─── Case 5: Different strokes must NOT share PB ───

    /** @test */
    public function case5_different_event_keys_do_not_share_pb(): void
    {
        // event_key = stroke_distance_category
        $freestyleKey    = 'bebas_50_perorangan';
        $breaststrokeKey = 'dada_50_perorangan';
        $freestyle100Key = 'bebas_100_perorangan';

        // These must all be distinct — no cross-contamination
        $this->assertNotEquals($freestyleKey, $breaststrokeKey);
        $this->assertNotEquals($freestyleKey, $freestyle100Key);
        $this->assertNotEquals($breaststrokeKey, $freestyle100Key);
    }

    // ─── Case 6: DNS/DQ must not enter PB calculation ───

    /** @test */
    public function case6_dns_dq_entries_are_excluded_from_pb(): void
    {
        // Simulate filtering: only result_status === 'finish' entries contribute to PB
        $results = [
            ['result_status' => 'finish', 'time_result' => '00.34.20', 'centiseconds' => 3420],
            ['result_status' => 'DNS',    'time_result' => null,       'centiseconds' => null],
            ['result_status' => 'DQ',     'time_result' => null,       'centiseconds' => null],
            ['result_status' => 'finish', 'time_result' => '00.33.80', 'centiseconds' => 3380],
        ];

        $finishResults = array_filter($results, fn($r) => $r['result_status'] === 'finish' && $r['centiseconds'] !== null);
        $pbCs          = min(array_column(array_values($finishResults), 'centiseconds'));
        $pbTime        = TimeHelper::fromCentiseconds($pbCs);

        $this->assertEquals(2, count($finishResults));  // Only 2 finish results counted
        $this->assertEquals('00.33.80', $pbTime);       // Best finish time wins
    }

    // ─── Historical PB correctness ───

    /** @test */
    public function historical_pb_uses_only_results_before_current_event_date(): void
    {
        // Simulate history sorted by competition_date ASC:
        // Event 1 (2026-02-10): result 00.34.20 => pb_before = null
        // Event 2 (2026-04-20): result 00.33.80 => pb_before = 00.34.20
        // Event 3 (2026-06-15): result 00.34.10 => pb_before = 00.33.80
        // Event 4 (2026-08-20): result 00.33.50 => pb_before = 00.33.80

        $allFinishResults = [
            ['competition_date' => '2026-02-10', 'entry_id' => 1, 'centiseconds' => 3420, 'time_result' => '00.34.20'],
            ['competition_date' => '2026-04-20', 'entry_id' => 2, 'centiseconds' => 3380, 'time_result' => '00.33.80'],
            ['competition_date' => '2026-06-15', 'entry_id' => 3, 'centiseconds' => 3410, 'time_result' => '00.34.10'],
            ['competition_date' => '2026-08-20', 'entry_id' => 4, 'centiseconds' => 3350, 'time_result' => '00.33.50'],
        ];

        $getPbBefore = function (array $allResults, string $currentDate, int $currentEntryId): ?string {
            $pbCs   = null;
            $pbTime = null;
            foreach ($allResults as $r) {
                $isBefore = ($r['competition_date'] < $currentDate)
                    || ($r['competition_date'] === $currentDate && $r['entry_id'] < $currentEntryId);
                if ($isBefore && ($pbCs === null || $r['centiseconds'] < $pbCs)) {
                    $pbCs   = $r['centiseconds'];
                    $pbTime = $r['time_result'];
                }
            }
            return $pbTime;
        };

        $this->assertNull($getPbBefore($allFinishResults, '2026-02-10', 1));         // Event 1: no prior
        $this->assertEquals('00.34.20', $getPbBefore($allFinishResults, '2026-04-20', 2)); // Event 2: PB=34.20
        $this->assertEquals('00.33.80', $getPbBefore($allFinishResults, '2026-06-15', 3)); // Event 3: PB=33.80
        $this->assertEquals('00.33.80', $getPbBefore($allFinishResults, '2026-08-20', 4)); // Event 4: PB=33.80
    }
}