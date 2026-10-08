@extends('layouts.app')

@section('title', 'Bonus Saya - AGANDA')
@section('page-title', 'Bonus Saya')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <header>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">Keuangan Member</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Bonus Saya</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan komisi dan penggunaan bonus akun Anda.</p>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan bonus pribadi">
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Bonus</p>
                <p class="mt-2 text-xl font-bold text-slate-900">Rp {{ number_format($totalBonus, 0, ',', '.') }}</p>
            </article>
            <article class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-5 shadow-sm">
                <p class="text-sm text-emerald-800">Saldo Tersedia</p>
                <p class="mt-2 text-xl font-bold text-emerald-800">Rp {{ number_format($availableBonus, 0, ',', '.') }}</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Digunakan untuk Paket</p>
                <p class="mt-2 text-xl font-bold text-slate-900">Rp {{ number_format($usedForPackage, 0, ',', '.') }}</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Pengajuan Pencairan</p>
                <p class="mt-2 text-xl font-bold text-slate-900">Rp {{ number_format($cashWithdrawals, 0, ',', '.') }}</p>
            </article>
        </section>

        <section class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">Bonus Sponsor · Line 1</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <p class="text-sm text-slate-500">{{ $line1Count }} orang</p>
                    <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($line1Amount, 0, ',', '.') }}</p>
                </div>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">Bonus Pasangan · Line 2</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <p class="text-sm text-slate-500">{{ $line2Count }} pairing</p>
                    <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($line2Amount, 0, ',', '.') }}</p>
                </div>
            </article>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Riwayat Bonus Saya</h2>
                <p class="mt-1 text-xs text-slate-500">Hanya transaksi bonus milik akun Anda yang ditampilkan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Jenis</th>
                            <th class="px-5 py-3">Keterangan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-3 text-sm text-slate-600">
                                    {{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-sm font-medium text-slate-800">
                                    {{ $transaction->type === 'line_1' ? 'Sponsor · Line 1' : ($transaction->type === 'line_2_pairing' ? 'Pasangan · Line 2' : 'Penyesuaian') }}
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-600">{{ $transaction->description ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $transaction->status === 'confirmed' ? 'Dikonfirmasi' : ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-right text-sm font-bold text-slate-900">
                                    Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                                    Belum ada transaksi bonus untuk akun Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">{{ $transactions->links() }}</div>
            @endif
        </section>
    </div>
@endsection
