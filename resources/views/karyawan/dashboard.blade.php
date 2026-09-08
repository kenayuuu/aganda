@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('page-title', 'Dashboard Karyawan')

@section('content')

    <div class="mx-auto max-w-7xl">

        <div class="mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Dashboard Karyawan
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola group, anggota, jaringan, dan aktivitas AGANDA Anda.
                </p>
            </div>
        </div>

        <div class="mb-6 rounded-2xl border border-red-100 bg-gradient-to-r from-red-50 to-white p-6 shadow-sm">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-xl font-bold text-white shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">
                            Selamat datang,
                        </p>

                        <h2 class="mt-0.5 text-xl font-bold text-slate-900">
                            {{ auth()->user()->name }}
                        </h2>

                        <div class="mt-1 flex flex-wrap items-center gap-2">

                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                Karyawan
                            </span>

                            <span class="text-xs text-slate-400">
                                {{ auth()->user()->member_id }}
                            </span>

                        </div>
                    </div>

                </div>

                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                    <p class="text-xs text-slate-400">
                        Status Akun
                    </p>

                    <div class="mt-1 flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                        <span class="text-sm font-semibold text-emerald-600">
                            Aktif
                        </span>
                    </div>
                </div>

            </div>

        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm text-slate-500">
                            Group Saya
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $totalGroups ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m8-8a4 4 0 100-8 4 4 0 000 8zm6-3a4 4 0 110 8m0 0v3m0-3h3m-3 0h-3" />

                        </svg>

                    </div>

                </div>

                <a href="{{ route('aganda.groups.index') }}"
                    class="mt-4 inline-flex text-xs font-semibold text-red-600 hover:text-red-700">

                    Lihat group

                    <span class="ml-1">→</span>

                </a>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm text-slate-500">
                            Total Anggota
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $totalMembers ?? 0 }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />

                        </svg>

                    </div>

                </div>

                <a href="{{ route('aganda.groups.index') }}"
                    class="mt-4 inline-flex text-xs font-semibold text-blue-600 hover:text-blue-700">

                    Lihat anggota

                    <span class="ml-1">→</span>

                </a>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm text-slate-500">
                            Bonus
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            Rp {{ number_format($totalBonus ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-12V5m0 14v-2m8-5a8 8 0 10-16 0 8 8 0 0016 0z" />

                        </svg>

                    </div>

                </div>

                <a href="#" class="mt-4 inline-flex text-xs font-semibold text-amber-600 hover:text-amber-700">

                    Lihat bonus

                    <span class="ml-1">→</span>

                </a>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm text-slate-500">
                            Reward
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            Rp {{ number_format($totalReward ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 12c0 5.591 3.824 10.291 9 11.622C17.176 22.291 21 17.591 21 12c0-1.042-.133-2.054-.382-3.016z" />

                        </svg>

                    </div>

                </div>

                <a href="#" class="mt-4 inline-flex text-xs font-semibold text-emerald-600 hover:text-emerald-700">

                    Lihat reward

                    <span class="ml-1">→</span>

                </a>

            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                <div class="border-b border-slate-100 px-6 py-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Group Saya
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Group yang Anda kelola sebagai karyawan.
                            </p>
                        </div>

                        <a href="{{ route('aganda.groups.index') }}"
                            class="text-sm font-semibold text-red-600 hover:text-red-700">

                            Lihat Semua

                        </a>

                    </div>

                </div>

                <div class="divide-y divide-slate-100">

                    @forelse ($latestGroups ?? [] as $group)
                        <div class="flex items-center justify-between gap-4 px-6 py-4">

                            <div class="min-w-0">

                                <p class="truncate font-semibold text-slate-900">
                                    {{ $group->kode_group }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $group->packageKegiatan->nama_paket ?? '-' }}
                                </p>

                            </div>

                            <div class="flex shrink-0 items-center gap-3">

                                <span class="hidden text-xs text-slate-400 sm:block">
                                    {{ $group->members_count ?? 0 }} anggota
                                </span>

                                <a href="{{ route('aganda.groups.show', $group) }}"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">

                                    Detail

                                </a>

                            </div>

                        </div>

                    @empty

                        <div class="px-6 py-10 text-center">

                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />

                                </svg>

                            </div>

                            <p class="mt-3 text-sm font-semibold text-slate-700">
                                Belum ada group
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Anda belum memiliki group AGANDA.
                            </p>

                            <a href="{{ route('aganda.groups.create') }}"
                                class="mt-4 inline-flex rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700">

                                Buat Group

                            </a>

                        </div>
                    @endforelse

                </div>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Akses Cepat
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Fitur yang sering digunakan.
                    </p>

                </div>

                <div class="space-y-2 p-4">

                    <a href="{{ route('aganda.groups.index') }}"
                        class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-slate-50">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />

                            </svg>

                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Group Saya
                            </p>

                            <p class="text-xs text-slate-400">
                                Kelola group dan anggota
                            </p>
                        </div>

                    </a>

                    <a href="{{ route('aganda.groups.index') }}"
                        class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-slate-50">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />

                            </svg>

                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Struktur Group
                            </p>

                            <p class="text-xs text-slate-400">
                                Lihat jaringan anggota
                            </p>
                        </div>

                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-slate-50">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-12V5m0 14v-2m8-5a8 8 0 10-16 0 8 8 0 0016 0z" />

                            </svg>

                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Bonus & Reward
                            </p>

                            <p class="text-xs text-slate-400">
                                Lihat pendapatan Anda
                            </p>
                        </div>

                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-slate-50">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                            </svg>

                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                Notifikasi
                            </p>

                            <p class="text-xs text-slate-400">
                                Lihat pemberitahuan terbaru
                            </p>
                        </div>

                    </a>

                </div>

            </div>

        </div>

    </div>

@endsection
