@extends('layouts.app')

@section('title', 'Pembayaran Saya - AGANDA')
@section('page-title', 'Pembayaran Saya')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <header>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">Keuangan Member</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pembayaran Saya</h1>
            <p class="mt-1 text-sm text-slate-500">Riwayat pembayaran paket yang terhubung dengan akun Anda.</p>
        </header>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Riwayat Pembayaran</h2>
                <p class="mt-1 text-xs text-slate-500">Data pembayaran bersifat baca-saja. Hubungi admin jika ada informasi yang perlu diperbaiki.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Paket</th>
                            <th class="px-5 py-3">Jenis Pembayaran</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-3 text-sm text-slate-600">
                                    {{ $payment->paid_at?->format('d/m/Y H:i') ?? $payment->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-sm font-semibold text-slate-800">{{ $payment->packageKegiatan->nama_paket ?? '-' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Harga paket Rp {{ number_format((float) $payment->package_price, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-5 py-3 text-sm text-slate-700">
                                    {{ $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan' }}
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $paymentStatusClass = match ($payment->status) {
                                            'paid' => 'bg-emerald-50 text-emerald-700',
                                            'pending' => 'bg-amber-50 text-amber-700',
                                            'cancelled', 'failed' => 'bg-red-50 text-red-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentStatusClass }}">
                                        {{ match ($payment->status) {
                                            'paid' => 'Dibayar',
                                            'pending' => 'Menunggu',
                                            'cancelled' => 'Dibatalkan',
                                            'failed' => 'Gagal',
                                            default => ucfirst($payment->status),
                                        } }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-right text-sm font-bold text-slate-900">
                                    Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <p class="text-sm font-semibold text-slate-600">Belum ada riwayat pembayaran</p>
                                    <p class="mt-1 text-xs text-slate-400">Pembayaran paket Anda akan tampil di sini setelah dicatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($payments->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">{{ $payments->links() }}</div>
            @endif
        </section>
    </div>
@endsection
