<?php

namespace Tests\Unit;

use App\Models\Competition;
use Carbon\Carbon;
use Tests\TestCase;

class CompetitionStatusTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_status_is_akan_datang_when_today_is_before_start_date(): void
    {
        Carbon::setTestNow('2026-10-10');

        $status = Competition::determineStatus('2026-10-15', '2026-10-17');
        $this->assertSame('akan_datang', $status);

        $competition = new Competition([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-17',
        ]);
        $this->assertSame('akan_datang', $competition->status);
        $this->assertSame('Akan Datang', $competition->status_label);
    }

    public function test_status_is_berlangsung_on_start_date(): void
    {
        Carbon::setTestNow('2026-10-15');

        $status = Competition::determineStatus('2026-10-15', '2026-10-17');
        $this->assertSame('berlangsung', $status);

        $competition = new Competition([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-17',
        ]);
        $this->assertSame('berlangsung', $competition->status);
        $this->assertSame('Berlangsung', $competition->status_label);
    }

    public function test_status_is_berlangsung_between_dates(): void
    {
        Carbon::setTestNow('2026-10-16');

        $status = Competition::determineStatus('2026-10-15', '2026-10-17');
        $this->assertSame('berlangsung', $status);

        $competition = new Competition([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-17',
        ]);
        $this->assertSame('berlangsung', $competition->status);
    }

    public function test_status_is_berlangsung_on_end_date(): void
    {
        Carbon::setTestNow('2026-10-17');

        $status = Competition::determineStatus('2026-10-15', '2026-10-17');
        $this->assertSame('berlangsung', $status);

        $competition = new Competition([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-17',
        ]);
        $this->assertSame('berlangsung', $competition->status);
    }

    public function test_status_is_selesai_after_end_date(): void
    {
        Carbon::setTestNow('2026-10-18');

        $status = Competition::determineStatus('2026-10-15', '2026-10-17');
        $this->assertSame('selesai', $status);

        $competition = new Competition([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-17',
        ]);
        $this->assertSame('selesai', $competition->status);
        $this->assertSame('Selesai', $competition->status_label);
    }

    public function test_status_keterangan(): void
    {
        Carbon::setTestNow('2026-10-10');

        $competition = new Competition([
            'start_date' => '2026-10-15',
            'end_date'   => '2026-10-17',
        ]);
        $this->assertSame('Mulai dalam 5 hari', $competition->status_keterangan);

        Carbon::setTestNow('2026-10-16');
        $this->assertSame('Pertandingan sedang berlangsung', $competition->status_keterangan);

        Carbon::setTestNow('2026-10-18');
        $this->assertSame('Jadwal pertandingan telah selesai', $competition->status_keterangan);
    }
}
