@extends('layouts.parent')

@section('title', 'Riwayat Perlombaan - ' . $athlete->name)
@section('header_title', 'Riwayat Perlombaan Anak')

@section('styles')
<style>
    .eh-profile-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.9) 0%, rgba(6,15,34,0.95) 100%);
        border: 1px solid var(--glass-border); border-radius: 1.25rem;
        padding: 1.5rem; position: relative; overflow: hidden;
    }
    .eh-profile-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.2) 50%, transparent);
    }
    .eh-big-avatar {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(26,107,181,0.25); border: 2px solid rgba(91,168,232,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem; font-weight: 900; color: #5ba8e8;
        overflow: hidden; flex-shrink: 0;
    }
    .eh-big-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .info-tag {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .25rem .65rem; border-radius: 2rem;
        background: rgba(10,22,40,.5); border: 1px solid var(--glass-border);
        font-size: .75rem; font-weight: 600; color: var(--text-muted);
    }
    .eh-stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media(max-width:600px){ .eh-stat-row { grid-template-columns: 1fr; } }
    .eh-mini-stat {
        background: linear-gradient(160deg, rgba(12,24,48,0.85) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid var(--glass-border); border-radius: 1rem; padding: 1.15rem;
        text-align: center; position: relative; overflow: hidden;
    }
    .eh-mini-stat::before { content:''; position:absolute; top:0; left:0; right:0; height:1px; background: linear-gradient(90deg,transparent,rgba(200,216,236,.18) 50%,transparent); }
    .eh-mini-stat-val { font-size: 1.85rem; font-weight: 900; letter-spacing: -.03em; line-height: 1; }
    .eh-mini-stat-val.blue { color: #5ba8e8; } .eh-mini-stat-val.green { color: #4ade80; } .eh-mini-stat-val.gold { color: #e8c96d; }
    .eh-mini-stat-lbl { font-size: .67rem; font-weight: 700; text-transform: uppercase; letter-spacing: .75px; color: var(--text-muted); margin-top: .35rem; }

    .pb-cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: .85rem; }
    .pb-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.8) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid rgba(201,168,76,.25); border-radius: 1rem; padding: 1.1rem;
        position: relative; overflow: hidden;
    }
    .pb-card::before { content:''; position:absolute; top:0; left:0; right:0; height:1px; background: linear-gradient(90deg,transparent,rgba(201,168,76,.35) 50%,transparent); }
    .pb-card-ev   { font-size:.78rem; font-weight:700; color:var(--silver-200,#dde8f4); margin-bottom:.2rem; }
    .pb-card-date { font-size:.68rem; color:var(--text-muted); margin-bottom:.75rem; }
    .pb-card-time { font-family:monospace; font-size:1.6rem; font-weight:900; color:#e8c96d; }
    .pb-card-sub  { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#c9a84c; margin-top:.15rem; }
    .pb-card-comp { font-size:.68rem; color:var(--text-muted); margin-top:.65rem; padding-top:.55rem; border-top:1px solid rgba(201,168,76,.12); }

    .eh-section { background: linear-gradient(160deg,rgba(10,22,40,.8) 0%,rgba(6,15,34,.9) 100%); border: 1px solid var(--glass-border); border-radius:1.25rem; overflow:hidden; margin-bottom:1.15rem; }
    .eh-section-header { background: linear-gradient(135deg,#0369a1 0%,#1e3a5f 100%); padding:.85rem 1.5rem; display:flex; align-items:center; justify-content:space-between; }
    .eh-section-title { font-size:.92rem; font-weight:700; color:#fff; }
    .eh-section-badge { font-size:.7rem; font-weight:700; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.2); color:#fff; padding:.2rem .6rem; border-radius:2rem; }
    .eh-footer { background:rgba(6,15,34,.5); border-top:1px solid var(--glass-border); padding:.5rem 1.5rem; font-size:.7rem; color:var(--text-muted); }

    .time-cell { font-family:monospace; font-size:1.15rem; font-weight:900; }
    .time-cell.best { color:#4ade80; } .time-cell.normal { color:#5ba8e8; }
    .pb-cell { font-family:monospace; font-size:.88rem; font-weight:700; color:#a78bfa; }
    .diff-faster { font-family:monospace; font-size:.82rem; font-weight:800; color:#4ade80; }
    .diff-slower { font-family:monospace; font-size:.82rem; font-weight:800; color:#f87171; }
    .diff-equal  { font-family:monospace; font-size:.82rem; font-weight:800; color:var(--text-muted); }
    .diff-first  { font-size:.75rem; font-style:italic; color:var(--text-muted); }
    .status-dns { display:inline-block; padding:.2rem .5rem; border-radius:.35rem; font-size:.7rem; font-weight:700; background:rgba(248,113,113,.12); border:1px solid rgba(248,113,113,.2); color:#f87171; }
    .status-dq  { display:inline-block; padding:.2rem .5rem; border-radius:.35rem; font-size:.7rem; font-weight:700; background:rgba(251,146,60,.12); border:1px solid rgba(251,146,60,.2); color:#fb923c; }
    .empty-state { text-align:center; padding:4rem 2rem; }
    .empty-icon  { font-size:3rem; opacity:.35; margin-bottom:1rem; }
    .empty-title { font-size:1rem; font-weight:700; color:var(--silver-200,#dde8f4); margin-bottom:.4rem; }
    .empty-desc  { font-size:.82rem; color:var(--text-muted); font-style:italic; }
    .btn-back { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1rem; border-radius:.65rem; font-size:.82rem; font-weight:600; text-decoration:none; color:var(--silver-300,#c8d8ec); border:1px solid rgba(91,168,232,.2); background:transparent; transition:all .2s ease; }
    .btn-back:hover { background:rgba(91,168,232,.1); color:#edf4fc; }
</style>
@endsection

@section('content')
<div style="display:flex;flex-direction:column;gap:1.75rem;">

    {{-- Profile --}}
    <div class="eh-profile-card">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;flex:1;">
                <div class="eh-big-avatar">
                    @if($athlete->photo)
                        <img src="{{ asset('storage/'.$athlete->photo) }}" alt="{{ $athlete->name }}">
                    @else
                        {{ strtoupper(substr($athlete->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:var(--silver-200,#dde8f4);margin-bottom:.45rem;">📊 {{ $athlete->name }}</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                        <span class="info-tag">🏫 {{ $athlete->currentClass->name ?? '-' }}</span>
                        <span class="info-tag">KU {{ $athlete->ku ?? '-' }}</span>
                        <span class="info-tag">{{ $athlete->gender === 'L' ? '♂ PA' : '♀ PI' }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('parent.dashboard') }}" class="btn-back">◀ Dashboard</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="eh-stat-row">
        <div class="eh-mini-stat"><div class="eh-mini-stat-val blue">{{ count($historyRows) }}</div><div class="eh-mini-stat-lbl">Nomor Diikuti</div></div>
        <div class="eh-mini-stat"><div class="eh-mini-stat-val green">{{ count($historyByEvent) }}</div><div class="eh-mini-stat-lbl">Kombinasi Gaya</div></div>
        <div class="eh-mini-stat"><div class="eh-mini-stat-val gold">{{ count($currentPBs) }}</div><div class="eh-mini-stat-lbl">Personal Best</div></div>
    </div>

    {{-- PBs --}}
    @if(count($currentPBs) > 0)
    <div class="data-card">
        <div class="card-header">
            <div>
                <h3 class="card-title">🏅 Personal Best Saat Ini</h3>
                <p style="font-size:.78rem;color:var(--text-muted);margin-top:.2rem;">Waktu terbaik untuk setiap nomor lomba</p>
            </div>
            <span style="background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);color:#e8c96d;padding:.35rem .85rem;border-radius:2rem;font-size:.76rem;font-weight:700;">{{ count($currentPBs) }} PB</span>
        </div>
        <div class="pb-cards-grid">
            @foreach($currentPBs as $pb)
            <div class="pb-card">
                <div class="pb-card-ev">🏊 {{ $pb['event_label'] }}</div>
                <div class="pb-card-date">{{ $pb['competition_date'] }}</div>
                <div class="pb-card-time">{{ $pb['time_result'] }}</div>
                <div class="pb-card-sub">Waktu Terbaik</div>
                <div class="pb-card-comp">📍 {{ $pb['competition_name'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- History --}}
    @if(count($historyByEvent) > 0)
        @foreach($historyByEvent as $evData)
        <div class="eh-section">
            <div class="eh-section-header">
                <span class="eh-section-title">🏊 {{ $evData['event_label'] }}</span>
                <span class="eh-section-badge">{{ count($evData['history']) }} Riwayat</span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Perlombaan</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Hasil</th>
                            <th style="text-align:center;">PB Sebelumnya</th>
                            <th style="text-align:center;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evData['history'] as $row)
                        <tr>
                            <td><div style="font-weight:700;color:var(--silver-200,#dde8f4);font-size:.85rem;">{{ $row['competition_name'] }}</div></td>
                            <td style="text-align:center;white-space:nowrap;font-size:.85rem;">{{ $row['competition_date_formatted'] }}</td>
                            <td style="text-align:center;">
                                @if($row['result_status']==='finish')
                                    <span class="time-cell {{ $row['is_first_valid_result'] ? 'best' : 'normal' }}">{{ $row['time_result'] }}</span>
                                    @if($row['is_first_valid_result'])<div style="font-size:.6rem;color:#4ade80;font-weight:700;">★ PB Awal</div>@endif
                                @elseif($row['result_status']==='DNS')<span class="status-dns">DNS</span>
                                @elseif($row['result_status']==='DQ')<span class="status-dq">DQ</span>
                                @else<span style="color:var(--text-muted);font-size:.78rem;">—</span>@endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status']==='finish' && $row['pb_before_event'])
                                    <span class="pb-cell">{{ $row['pb_before_event'] }}</span>
                                @else
                                    <span style="color:var(--text-muted);font-size:.78rem;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status']==='finish')
                                    @if($row['is_first_valid_result'])<span class="diff-first">PB Awal</span>
                                    @elseif($row['difference']!==null)
                                        @if($row['is_faster']===true)<span class="diff-faster">{{ $row['difference'] }}</span>
                                        @elseif($row['difference']==='00.00.00')<span class="diff-equal">00.00.00</span>
                                        @else<span class="diff-slower">{{ $row['difference'] }}</span>@endif
                                    @else<span style="color:var(--text-muted);font-size:.78rem;">—</span>@endif
                                @else<span style="color:var(--text-muted);font-size:.78rem;">—</span>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="eh-footer">
                <span style="color:#4ade80;">Hijau = lebih cepat</span> &bull;
                <span style="color:#f87171;">Merah = lebih lambat</span> &bull;
                PB Awal = belum ada data sebelumnya
            </div>
        </div>
        @endforeach
    @else
        <div class="data-card">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Belum Ada Riwayat</div>
                <p class="empty-desc">{{ $athlete->name }} belum memiliki catatan perlombaan.</p>
            </div>
        </div>
    @endif

</div>
@endsection