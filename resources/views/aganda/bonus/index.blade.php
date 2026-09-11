@extends('layouts.app')

@section('content')
    <div class="space-y-5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">
                    Bonus
                </h1>

                <p class="mt-1 text-xs text-slate-500">
                    Perhitungan komisi dan bonus member serta karyawan AGANDA.
                </p>
            </div>

            <a href="{{ route('admin.bonus.payments.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-red-700">
                Kelola Pembayaran
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form action="{{ route('admin.bonus.index') }}" method="GET" class="flex flex-col gap-2 sm:flex-row">

                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, email, atau Member ID"
                    class="flex-1 rounded-lg border border-slate-200 px-3 py-2.5 text-xs outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">

                <button type="submit"
                    class="rounded-lg bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800">
                    Cari
                </button>

                <a href="{{ route('admin.bonus.index') }}"
                    class="rounded-lg border border-slate-200 px-5 py-2.5 text-center text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-3">

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-medium text-slate-500">
                    Total Member & Karyawan
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $totalUsers }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-medium text-slate-500">
                    Total Komisi
                </p>

                <p class="mt-1 text-xl font-bold text-emerald-600">
                    Rp {{ number_format($totalBonus, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-medium text-slate-500">
                    Saldo Bonus Tersedia
                </p>

                <p class="mt-1 text-xl font-bold text-amber-600">
                    Rp {{ number_format($totalAvailableBonus, 0, ',', '.') }}
                </p>
            </div>

        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">
                    Data Bonus
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Komisi, bonus pairing, pembayaran paket, dan saldo setiap pengguna.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left">

                    <thead class="bg-slate-50">
                        <tr class="border-b border-slate-200">

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Pengguna
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Paket
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                DP
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Sisa Paket
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Line 1
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Line 2
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Total Bonus
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Dipakai
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Saldo
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse($bonusData as $data)
                            @php
                                $member = $data['member'];
                            @endphp

                            <tr class="transition hover:bg-slate-50">

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">

                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                            {{ $member->role === 'karyawan' ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600' }}
                                            text-xs font-bold">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-bold text-slate-900">
                                                {{ $member->name }}
                                            </p>

                                            <div class="mt-0.5 flex items-center gap-1.5">
                                                <span class="max-w-[130px] truncate text-[9px] text-slate-400">
                                                    {{ $member->member_id ?: $member->email }}
                                                </span>

                                                <span
                                                    class="rounded-full px-1.5 py-0.5 text-[8px] font-bold uppercase
                                                    {{ $member->role === 'karyawan' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }}">
                                                    {{ $member->role }}
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-900">
                                        Rp {{ number_format($data['package_price'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-900">
                                        Rp {{ number_format($data['deposit'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-bold text-amber-700">
                                        Rp {{ number_format($data['remaining_package'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-900">
                                        {{ $data['line1_count'] }} orang
                                    </p>

                                    <p class="text-[10px] text-emerald-600">
                                        Rp {{ number_format($data['line1_bonus'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-900">
                                        {{ $data['line2_count'] }} pairing
                                    </p>

                                    <p class="text-[10px] text-emerald-600">
                                        Rp {{ number_format($data['line2_bonus'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-bold text-emerald-600">
                                        Rp {{ number_format($data['total_bonus'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[11px] font-semibold text-slate-900">
                                        Rp {{ number_format($data['package_payment'], 0, ',', '.') }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex rounded-lg bg-emerald-50 px-2 py-1.5 text-[10px] font-bold text-emerald-700">
                                        Rp {{ number_format($data['available_bonus'], 0, ',', '.') }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">

                                    <div
                                        class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4 1.12 4 2.5S14.21 18 12 18M12 5v14" />
                                        </svg>
                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-600">
                                        Belum ada data bonus.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Data bonus akan muncul setelah member atau karyawan mendapatkan bonus.
                                    </p>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            @if ($bonusData->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">
                    {{ $bonusData->onEachSide(1)->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection
