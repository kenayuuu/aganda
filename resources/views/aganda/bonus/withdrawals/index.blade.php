
@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Pencairan Bonus
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola pengajuan pencairan bonus member.
                </p>
            </div>
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
            <form action="{{ route(auth()->user()->role === 'karyawan' ? 'karyawan.bonus.withdrawals.index' : 'admin.bonus.withdrawals.index') }}"
                method="GET" class="grid gap-4 md:grid-cols-3">

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Cari Member
                    </label>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, email, atau nomor telepon"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Status
                    </label>

                    <select name="status"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        <option value="">Semua</option>
                        <option value="pending" @selected(request('status') === 'pending')>
                            Menunggu
                        </option>
                        <option value="approved" @selected(request('status') === 'approved')>
                            Disetujui
                        </option>
                        <option value="paid" @selected(request('status') === 'paid')>
                            Dibayar
                        </option>
                        <option value="rejected" @selected(request('status') === 'rejected')>
                            Ditolak
                        </option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>
                            Dibatalkan
                        </option>
                    </select>
                </div>

                <div class="flex items-end">
                    <div class="flex gap-2">
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                            Filter
                        </button>

                        <a href="{{ route(auth()->user()->role === 'karyawan' ? 'karyawan.bonus.withdrawals.index' : 'admin.bonus.withdrawals.index') }}"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
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
                                Member
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Nominal
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Diajukan
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Diproses
                            </th>

                            <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($withdrawals as $withdrawal)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-5 py-4 text-sm font-medium text-slate-500">
                                    {{ $withdrawals->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">
                                        {{ $withdrawal->user->name ?? '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $withdrawal->user->email ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm font-bold text-slate-900">
                                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4">
                                    @if ($withdrawal->status === 'pending')
                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                            Menunggu
                                        </span>
                                    @elseif ($withdrawal->status === 'approved')
                                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600">
                                            Disetujui
                                        </span>
                                    @elseif ($withdrawal->status === 'paid')
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                            Dibayar
                                        </span>
                                    @elseif ($withdrawal->status === 'rejected')
                                        <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-600">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $withdrawal->requested_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $withdrawal->processed_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route(auth()->user()->role === 'karyawan' ? 'karyawan.bonus.withdrawals.show' : 'admin.bonus.withdrawals.show', $withdrawal) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800">
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="text-sm font-semibold text-slate-600">
                                        Belum ada pengajuan pencairan bonus.
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        Pengajuan dari member akan muncul di sini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($withdrawals->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

