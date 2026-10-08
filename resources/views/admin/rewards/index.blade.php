@extends('layouts.app')

@section('title', 'Monitoring Reward - AGANDA')
@section('page-title', 'Reward')

@section('content')
    @php
        $statusLabels = [
            'Belum Mulai' => 'bg-slate-100 text-slate-600',
            'Sedang Berjalan' => 'bg-blue-50 text-blue-700',
            'Hampir Tercapai' => 'bg-amber-50 text-amber-700',
            'Menunggu Verifikasi' => 'bg-orange-50 text-orange-700',
            'Tercapai' => 'bg-emerald-50 text-emerald-700',
        ];
    @endphp

    <div class="mx-auto max-w-[1600px] space-y-6">
        <header class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">AGANDA x ASIATUR</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Reward</h1>
                <p class="mt-1 text-sm text-slate-500">Monitoring pencapaian reward seluruh member AGANDA x ASIATUR</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.rewards.export') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0l4-4m-4 4l-4-4M5 17v3h14v-3" /></svg>
                    Export Data
                </a>
                <a href="{{ route('admin.rewards.history') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3.5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Riwayat Reward
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ session('error') }}</div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="Ringkasan reward member">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-medium text-slate-500">Total Member</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($summary['total_members']) }}</p><p class="mt-1 text-xs text-slate-400">Member aktif pada group</p></article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-medium text-slate-500">Member Mengejar Reward</p><p class="mt-2 text-2xl font-bold text-blue-700">{{ number_format($summary['members_pursuing']) }}</p><p class="mt-1 text-xs text-slate-400">Memiliki progress yang berjalan</p></article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-medium text-slate-500">Reward Tercapai</p><p class="mt-2 text-2xl font-bold text-emerald-700">{{ number_format($summary['achievements']) }}</p><p class="mt-1 text-xs text-slate-400">Pencapaian berdasarkan transaksi sukses</p></article>
            <article class="rounded-xl border border-orange-200 bg-orange-50/60 p-4 shadow-sm"><p class="text-xs font-medium text-orange-800">Menunggu Verifikasi</p><p class="mt-2 text-2xl font-bold text-orange-800">{{ number_format($summary['pending_verifications']) }}</p><p class="mt-1 text-xs text-orange-700">Target tercapai, belum diperiksa</p></article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-medium text-slate-500">Total Reward Diberikan</p><p class="mt-2 text-2xl font-bold text-amber-700">{{ number_format($summary['awarded']) }}</p><p class="mt-1 text-xs text-slate-400">Award terverifikasi admin</p></article>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="level-summary-title">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 id="level-summary-title" class="text-sm font-bold text-slate-900">Progress Reward Member</h2>
                <p class="mt-1 text-xs text-slate-500">Jumlah member dan progress dihitung dari pendaftaran aktif dengan pembayaran berhasil.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($levelSummaries as $level)
                    <div class="grid gap-3 px-5 py-3.5 md:grid-cols-[minmax(220px,1.4fr)_130px_130px_minmax(180px,1fr)] md:items-center">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-700">{{ $level['level'] }}</span>
                            <div class="min-w-0"><p class="truncate text-sm font-semibold text-slate-800">{{ $level['name'] }}</p><p class="text-xs text-slate-500">Target {{ number_format($level['target']) }} {{ $level['unit'] }}</p></div>
                        </div>
                        <p class="text-xs text-slate-600"><span class="font-bold text-slate-900">{{ number_format($level['chasing_count']) }}</span> mengejar</p>
                        <p class="text-xs text-slate-600"><span class="font-bold text-emerald-700">{{ number_format($level['achieved_count']) }}</span> tercapai</p>
                        <div><div class="mb-1 flex justify-between text-[11px] text-slate-500"><span>Progress keseluruhan</span><span>{{ $level['overall_percent'] }}%</span></div><div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-blue-600" style="width: {{ $level['overall_percent'] }}%"></div></div></div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="top-members-title">
            <div class="border-b border-slate-100 px-5 py-4"><h2 id="top-members-title" class="text-sm font-bold text-slate-900">Member Dengan Progress Tertinggi</h2><p class="mt-1 text-xs text-slate-500">Urutan berdasarkan persentase menuju reward berikutnya.</p></div>
            <div class="grid gap-3 p-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($topMembers as $row)
                    <article class="rounded-lg border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900">{{ $row['member']->name }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $row['member']->member_id ?? 'Tanpa Member ID' }}</p></div><span class="rounded-md bg-blue-50 px-2 py-1 text-xs font-bold text-blue-700">{{ $row['progress_percent'] }}%</span></div>
                        <p class="mt-3 text-xs font-semibold text-slate-700">Level {{ $row['next_reward']['level'] }} — {{ $row['next_reward']['name'] }}</p>
                        <div class="mt-2 flex justify-between text-xs text-slate-500"><span>{{ number_format($row['next_reward']['progress']) }} / {{ number_format($row['next_reward']['target']) }} {{ $row['next_reward']['unit'] }}</span><span>{{ max(0, $row['next_reward']['target'] - $row['next_reward']['progress']) }} lagi</span></div>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-blue-600" style="width: {{ $row['progress_percent'] }}%"></div></div>
                        <a href="{{ route('admin.rewards.show', [$row['group_id'], $row['member']->id]) }}" class="mt-3 inline-flex text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Detail <span class="ml-1">&rarr;</span></a>
                    </article>
                @empty
                    <p class="px-2 py-4 text-sm text-slate-500">Belum ada member dengan pembayaran sukses.</p>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="member-monitoring-title">
            <div class="border-b border-slate-100 px-5 py-4"><h2 id="member-monitoring-title" class="text-sm font-bold text-slate-900">Monitoring Pencapaian Member</h2><p class="mt-1 text-xs text-slate-500">Hanya pembayaran berstatus paid yang berkontribusi pada progress.</p></div>
            <form method="GET" action="{{ route('admin.rewards.index') }}" class="grid gap-3 border-b border-slate-100 bg-slate-50/70 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(240px,1fr)_180px_190px_auto_auto]">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama member atau Member ID..." class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <select name="level" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Semua Level</option>@foreach ($rewardLevels as $level)<option value="{{ $level['level'] }}" @selected((string) request('level') === (string) $level['level'])>Level {{ $level['level'] }} · {{ $level['name'] }}</option>@endforeach</select>
                <select name="status" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Semua Status</option><option value="not_started" @selected(request('status') === 'not_started')>Belum Mulai</option><option value="in_progress" @selected(request('status') === 'in_progress')>Sedang Berjalan</option><option value="achieved" @selected(request('status') === 'achieved')>Tercapai</option><option value="pending" @selected(request('status') === 'pending')>Menunggu Verifikasi</option></select>
                <select name="sort" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><option value="">Urutkan nama</option><option value="progress" @selected(request('sort') === 'progress')>Progress tertinggi</option></select>
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Filter</button>
                <a href="{{ route('admin.rewards.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1120px] text-left">
                    <thead class="bg-white text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3">Member</th><th class="px-4 py-3">Member ID</th><th class="px-4 py-3">Jemaah Berhasil</th><th class="px-4 py-3">Level Saat Ini</th><th class="px-4 py-3">Reward Berikutnya</th><th class="px-4 py-3">Progress</th><th class="px-4 py-3">%</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
+                    <tbody class="divide-y divide-slate-100">
+                        @forelse ($members as $row)
+                            @php($statusClass = match ($row['status']) {'Belum Mulai' => 'bg-slate-100 text-slate-600', 'Sedang Berjalan' => 'bg-blue-50 text-blue-700', 'Hampir Tercapai' => 'bg-amber-50 text-amber-700', 'Menunggu Verifikasi' => 'bg-orange-50 text-orange-700', 'Tercapai' => 'bg-emerald-50 text-emerald-700', default => 'bg-slate-100 text-slate-600'})
+                            <tr class="hover:bg-slate-50/70">
+                                <td class="px-4 py-3"><p class="text-sm font-semibold text-slate-900">{{ $row['member']->name }}</p><p class="mt-0.5 text-[11px] text-slate-500">{{ $row['group_code'] }}</p></td>
+                                <td class="px-4 py-3 text-xs text-slate-600">{{ $row['member']->member_id ?? '-' }}</td>
+                                <td class="px-4 py-3"><p class="text-sm font-semibold text-slate-800">{{ number_format($row['successful_jamaah_count']) }}</p><p class="text-[11px] text-slate-500">Rp {{ number_format($row['paid_payment_total'], 0, ',', '.') }} dibayar</p></td>
+                                <td class="px-4 py-3 text-xs font-medium text-slate-700">Level {{ $row['next_reward']['level'] }}</td>
+                                <td class="px-4 py-3 text-xs text-slate-700">{{ $row['next_reward']['name'] }}</td>
+                                <td class="px-4 py-3"><div class="min-w-32"><p class="mb-1 text-[11px] text-slate-600">{{ number_format($row['next_reward']['progress']) }} / {{ number_format($row['next_reward']['target']) }} {{ $row['next_reward']['unit'] }}</p><div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-blue-600" style="width: {{ $row['progress_percent'] }}%"></div></div></div></td>
+                                <td class="px-4 py-3 text-xs font-bold text-slate-700">{{ $row['progress_percent'] }}%</td>
+                                <td class="px-4 py-3"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">{{ $row['status'] }}</span></td>
+                                <td class="px-4 py-3 text-right"><a href="{{ route('admin.rewards.show', [$row['group_id'], $row['member']->id]) }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900">Lihat Detail</a><a href="{{ route('admin.rewards.show', [$row['group_id'], $row['member']->id]) }}#contributors" class="ml-3 text-xs font-semibold text-slate-600 hover:text-slate-900">Jemaah</a></td>
+                            </tr>
+                        @empty
+                            <tr><td colspan="9" class="px-5 py-12 text-center text-sm text-slate-500">Tidak ada data member sesuai filter.</td></tr>
+                        @endforelse
+                    </tbody>
+                </table>
+            </div>
+            @if ($members->hasPages())<div class="border-t border-slate-100 px-5 py-3">{{ $members->links() }}</div>@endif
+        </section>
+
+        <p class="text-xs leading-5 text-slate-400">Progress bersumber dari pendaftaran anggota aktif, pembayaran berstatus paid, dan pairing aktif dengan kedua peserta telah membayar. Status reward tidak dapat diedit manual.</p>
+    </div>
+@endsection
*** End Patch
