@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Pembayaran
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola pencatatan pembayaran calon secara manual.
                </p>
            </div>

            <a href="{{ route('admin.bonus.payments.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pembayaran
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('admin.bonus.payments.index') }}" method="GET" class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Cari Calon
                    </label>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, email, atau nomor telepon"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Jenis Pembayaran
                    </label>

                    <select name="payment_type"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        <option value="">Semua</option>
                        <option value="dp" @selected(request('payment_type') === 'dp')>
                            DP
                        </option>
                        <option value="pelunasan" @selected(request('payment_type') === 'pelunasan')>
                            Pelunasan
                        </option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Status
                    </label>

                    <select name="status"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        <option value="">Semua</option>
                        <option value="paid" @selected(request('status') === 'paid')>
                            Dibayar
                        </option>
                        <option value="pending" @selected(request('status') === 'pending')>
                            Pending
                        </option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>
                            Dibatalkan
                        </option>
                        <option value="failed" @selected(request('status') === 'failed')>
                            Gagal
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-2 md:col-span-4">
                    <button type="submit"
                        class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                        Filter
                    </button>

                    <a href="{{ route('admin.bonus.payments.index') }}"
                        class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px]">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                No
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Calon
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Paket
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Jenis
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Nominal
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Status
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Tanggal
                            </th>
                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Admin
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $payment)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-4 text-sm font-medium text-slate-500">
                                    {{ $payments->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $payment->calon->nama_lengkap }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $payment->calon->no_telepon ?: '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">
                                        {{ $payment->packageKegiatan->nama_paket ?? '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        Rp {{ number_format($payment->package_price, 0, ',', '.') }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($payment->payment_type === 'dp')
                                        <span
                                            class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                            DP
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600">
                                            Pelunasan
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm font-bold text-slate-900">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4">
                                    @if ($payment->status === 'paid')
                                        <span
                                            class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                            Dibayar
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span
                                            class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                            Pending
                                        </span>
                                    @elseif($payment->status === 'cancelled')
                                        <span
                                            class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-600">
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            Gagal
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $payment->paid_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $payment->confirmedBy->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <div class="text-sm font-semibold text-slate-600">
                                        Belum ada data pembayaran.
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        Pembayaran yang dicatat admin akan muncul di sini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
