@extends('layouts.app')

@section('title', 'Semua Group - AGANDA')

@section('page-title', 'Semua Group')

@section('content')

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="mb-8">

        <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">

            <div>

                <div class="mb-2 flex items-center gap-2 text-sm">

                    <a href="{{ route('admin.dashboard') }}" class="text-slate-400 transition hover:text-red-600">
                        Dashboard
                    </a>

                    <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" />
                    </svg>

                    <span class="font-medium text-slate-600">
                        Group
                    </span>

                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Semua Group
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Kelola dan pantau seluruh group AGANDA.
                </p>

            </div>


            {{-- Tombol Buat Group --}}

            <a href="{{ route('aganda.groups.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 hover:shadow-md">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>

                Buat Group

            </a>

        </div>

    </div>


    {{-- =========================================================
        ALERT SUCCESS
    ========================================================== --}}

    @if (session('success'))
        <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">

            <div
                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">

                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                </svg>

            </div>

            <div>

                <p class="text-sm font-semibold text-emerald-800">
                    Berhasil
                </p>

                <p class="mt-1 text-sm text-emerald-700">
                    {{ session('success') }}
                </p>

            </div>

        </div>
    @endif


    {{-- =========================================================
        FILTER & SEARCH
    ========================================================== --}}

    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form action="{{ route('aganda.groups.index') }}" method="GET" class="flex flex-col gap-4 lg:flex-row">

            {{-- Search --}}

            <div class="relative flex-1">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">

                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>

                </div>

                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari kode group, nama owner, atau Member ID..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-100">

            </div>


            {{-- Status --}}

            <div class="lg:w-52">

                <select name="status"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-100">

                    <option value="">
                        Semua Status
                    </option>

                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                        Aktif
                    </option>

                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>
                        Selesai
                    </option>

                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>
                        Dibatalkan
                    </option>

                </select>

            </div>


            {{-- Submit --}}

            <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>

                Cari

            </button>


            {{-- Reset --}}

            @if ($search || $status)
                <a href="{{ route('aganda.groups.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
            @endif

        </form>

    </div>


    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div class="mb-5 flex flex-col justify-between gap-2 sm:flex-row sm:items-center">

        <div>

            <p class="text-sm font-semibold text-slate-800">
                Daftar Group
            </p>

            <p class="mt-1 text-xs text-slate-400">

                Menampilkan
                {{ $groups->firstItem() ?? 0 }}
                -
                {{ $groups->lastItem() ?? 0 }}
                dari
                {{ $groups->total() }}
                group

            </p>

        </div>

    </div>


    {{-- =========================================================
        GROUP LIST
    ========================================================== --}}

    <div class="space-y-4">

        @forelse ($groups as $group)
            <div
                class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-slate-300 hover:shadow-md">

                <div class="p-5 sm:p-6">

                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">


                        {{-- =================================================
                            GROUP INFO
                        ================================================== --}}

                        <div class="flex min-w-0 items-start gap-4">

                            {{-- Icon --}}

                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">

                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>

                            </div>


                            {{-- Detail --}}

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <h2 class="text-base font-bold text-slate-900">
                                        {{ $group->kode_group }}
                                    </h2>


                                    {{-- Status --}}

                                    @if ($group->status === 'active' && auth()->user()->role === 'karyawan')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Aktif

                                        </span>
                                    @elseif ($group->status === 'completed')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>

                                            Selesai

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                            Dibatalkan

                                        </span>
                                    @endif

                                </div>


                                {{-- Owner --}}

                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">

                                    <span class="text-slate-600">

                                        Owner:

                                        <span class="font-semibold text-slate-800">
                                            {{ $group->owner->name ?? '-' }}
                                        </span>

                                    </span>

                                    @if ($group->owner)
                                        <span class="text-xs text-slate-400">
                                            {{ $group->owner->member_id }}
                                        </span>
                                    @endif

                                </div>


                                {{-- Package --}}

                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500">

                                    <span class="inline-flex items-center gap-1.5">

                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M20 7h-4V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z" />
                                        </svg>

                                        {{ $group->packageKegiatan->nama_paket ?? '-' }}

                                    </span>


                                    <span class="inline-flex items-center gap-1.5">

                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                                        </svg>

                                        {{ $group->packageKegiatan->tanggal_berlangsung
                                            ? \Carbon\Carbon::parse($group->packageKegiatan->tanggal_berlangsung)->translatedFormat('d F Y')
                                            : '-' }}

                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            STATISTIK GROUP
                        ================================================== --}}

                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:min-w-[380px]">

                            {{-- Anggota --}}

                            <div class="rounded-xl bg-slate-50 px-4 py-3">

                                <p class="text-xs font-medium text-slate-400">
                                    Anggota
                                </p>

                                <p class="mt-1 text-lg font-bold text-slate-800">
                                    {{ number_format($group->total_members) }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    aktif
                                </p>

                            </div>


                            {{-- Harga Paket --}}

                            <div class="rounded-xl bg-slate-50 px-4 py-3">

                                <p class="text-xs font-medium text-slate-400">
                                    Harga Paket
                                </p>

                                <p class="mt-1 truncate text-sm font-bold text-slate-800">

                                    Rp
                                    {{ number_format($group->packageKegiatan->harga ?? 0, 0, ',', '.') }}

                                </p>

                            </div>


                            {{-- Dibuat --}}

                            <div class="col-span-2 rounded-xl bg-slate-50 px-4 py-3 sm:col-span-1">

                                <p class="text-xs font-medium text-slate-400">
                                    Dibuat
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $group->created_at->format('d/m/Y') }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    {{ $group->created_at->diffForHumans() }}
                                </p>

                            </div>

                        </div>


                        {{-- =================================================
                            ACTION
                        ================================================== --}}

                        <div class="flex shrink-0 lg:ml-2">

                            <a href="{{ route('aganda.groups.show', $group->id) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 sm:w-auto">

                                Detail

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m9 5 7 7-7 7" />
                                </svg>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            {{-- =====================================================
                EMPTY STATE
            ====================================================== --}}

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a4 4 0 0 0-4-4h-1M9 20H4v-2a4 4 0 0 1 4-4h1M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                    </svg>

                </div>

                <h3 class="mt-5 text-base font-bold text-slate-800">
                    Tidak ada group
                </h3>

                @if ($search || $status)
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        Tidak ditemukan group yang sesuai dengan pencarian atau filter yang dipilih.
                    </p>

                    <a href="{{ route('aganda.groups.index') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Reset Filter
                    </a>
                @else
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        Belum ada group yang dibuat di sistem AGANDA.
                    </p>

                    <a href="{{ route('aganda.groups.create') }}"
                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>

                        Buat Group

                    </a>
                @endif

            </div>
        @endforelse

    </div>


    {{-- =========================================================
        PAGINATION
    ========================================================== --}}

    @if ($groups->hasPages())
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">

            {{ $groups->links() }}

        </div>
    @endif

@endsection
