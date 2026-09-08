@extends('layouts.app')

@section('title', 'Dashboard Admin - AGANDA')

@section('page-title', 'Dashboard')

@section('content')

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="mb-8">

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

            <div>

                <p class="mb-1 text-sm font-medium text-red-600">
                    Overview
                </p>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Selamat datang, {{ auth()->user()->name }} 👋
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Pantau seluruh aktivitas dan perkembangan sistem AGANDA.
                </p>

            </div>

            <div class="text-left sm:text-right">

                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                    Hari ini
                </p>

                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
        STATISTIK UTAMA
    ========================================================== --}}

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">


        {{-- Total Group --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total Group
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalGroups) }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        {{ number_format($activeGroups) }} group aktif
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1M12 12a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Total Member --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total Member
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalMembers) }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Member terdaftar
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Karyawan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Karyawan
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalKaryawan) }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Pengelola jaringan
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 14l9-5-9-5-9 5 9 5zM5 12v5c3 2 6 3 7 3s4-1 7-3v-5" />

                    </svg>

                </div>

            </div>

        </div>


        {{-- Anggota Group --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Anggota Group
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-900">
                        {{ number_format($totalGroupMembers) }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        {{ number_format($pendingGroupMembers) }} menunggu proses
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M18 20a6 6 0 00-12 0M12 14a4 4 0 100-8 4 4 0 000 8z" />

                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        BONUS & REWARD
    ========================================================== --}}

    <div class="mt-6 grid gap-5 lg:grid-cols-2">


        {{-- Bonus --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total Bonus
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        Rp {{ number_format($totalBonus, 0, ',', '.') }}
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4 1.12 4 2.5S14.21 18 12 18M12 5v14" />

                    </svg>

                </div>

            </div>

            <div class="mt-5 grid grid-cols-3 gap-3">

                <div class="rounded-xl bg-slate-50 p-3">

                    <p class="text-xs text-slate-400">
                        Pending
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-700">
                        Rp {{ number_format($pendingBonus, 0, ',', '.') }}
                    </p>

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    <p class="text-xs text-slate-400">
                        Approved
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-700">
                        Rp {{ number_format($approvedBonus, 0, ',', '.') }}
                    </p>

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    <p class="text-xs text-slate-400">
                        Paid
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-700">
                        Rp {{ number_format($paidBonus, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Reward --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total Reward
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        Rp {{ number_format($totalReward, 0, ',', '.') }}
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 3l2.7 5.46L21 9.38l-4.5 4.38 1.06 6.2L12 17.05 6.44 20l1.06-6.24L12 3z" />

                    </svg>

                </div>

            </div>

            <div class="mt-5 grid grid-cols-3 gap-3">

                <div class="rounded-xl bg-slate-50 p-3">

                    <p class="text-xs text-slate-400">
                        Pending
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-700">
                        Rp {{ number_format($pendingReward, 0, ',', '.') }}
                    </p>

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    <p class="text-xs text-slate-400">
                        Approved
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-700">
                        Rp {{ number_format($approvedReward, 0, ',', '.') }}
                    </p>

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    <p class="text-xs text-slate-400">
                        Paid
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-700">
                        Rp {{ number_format($paidReward, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        GROUP & MEMBER TERBARU
    ========================================================== --}}

    <div class="mt-6 grid gap-6 xl:grid-cols-2">


        {{-- Group Terbaru --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

                <div>

                    <h2 class="font-bold text-slate-900">
                        Group Terbaru
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Group yang baru dibuat
                    </p>

                </div>

                <a href="{{ route('aganda.groups.index') }}"
                    class="text-sm font-semibold text-red-600 hover:text-red-700">

                    Lihat semua

                </a>

            </div>


            <div class="divide-y divide-slate-100">

                @forelse ($latestGroups as $group)
                    <div class="flex items-center gap-4 px-6 py-4">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-sm font-bold text-red-600">

                            {{ strtoupper(substr($group->kode_group, 0, 1)) }}

                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="truncate text-sm font-semibold text-slate-800">
                                {{ $group->kode_group }}
                            </p>

                            <p class="mt-1 truncate text-xs text-slate-500">
                                Owner:
                                {{ $group->owner->name ?? '-' }}
                            </p>

                        </div>

                        <div class="text-right">

                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $group->status === 'active'
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : ($group->status === 'completed'
                                        ? 'bg-blue-50 text-blue-600'
                                        : 'bg-red-50 text-red-600') }}">

                                {{ ucfirst($group->status) }}

                            </span>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ $group->created_at->diffForHumans() }}
                            </p>

                        </div>

                    </div>

                @empty

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm text-slate-400">
                            Belum ada group.
                        </p>

                    </div>
                @endforelse

            </div>

        </div>


        {{-- Member Terbaru --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

                <div>

                    <h2 class="font-bold text-slate-900">
                        Member Terbaru
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Anggota yang baru ditambahkan
                    </p>

                </div>

                <a href="#" class="text-sm font-semibold text-red-600 hover:text-red-700">

                    Lihat semua

                </a>

            </div>


            <div class="divide-y divide-slate-100">

                @forelse ($latestMembers as $member)
                    <div class="flex items-center gap-4 px-6 py-4">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-600">

                            {{ strtoupper(substr($member->calon->nama_lengkap ?? 'M', 0, 1)) }}

                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="truncate text-sm font-semibold text-slate-800">
                                {{ $member->calon->nama_lengkap ?? '-' }}
                            </p>

                            <p class="mt-1 truncate text-xs text-slate-500">

                                {{ $member->group->kode_group ?? '-' }}

                                ·

                                Direkrut oleh
                                {{ $member->registeredBy->name ?? '-' }}

                            </p>

                        </div>

                        <div class="text-right">

                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $member->status === 'active'
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : ($member->status === 'pending'
                                        ? 'bg-amber-50 text-amber-600'
                                        : 'bg-red-50 text-red-600') }}">

                                {{ ucfirst($member->status) }}

                            </span>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ $member->created_at->diffForHumans() }}
                            </p>

                        </div>

                    </div>

                @empty

                    <div class="px-6 py-10 text-center">

                        <p class="text-sm text-slate-400">
                            Belum ada member.
                        </p>

                    </div>
                @endforelse

            </div>

        </div>

    </div>


    {{-- =========================================================
        RINGKASAN GROUP
    ========================================================== --}}

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-6">

            <h2 class="font-bold text-slate-900">
                Ringkasan Group
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Distribusi group berdasarkan status
            </p>

        </div>


        <div class="grid gap-4 sm:grid-cols-3">


            {{-- Active --}}
            <div class="rounded-xl bg-emerald-50 p-4">

                <div class="flex items-center justify-between">

                    <p class="text-sm font-medium text-emerald-700">
                        Aktif
                    </p>

                    <span class="text-2xl font-bold text-emerald-700">
                        {{ $groupStatus['active'] ?? 0 }}
                    </span>

                </div>

            </div>


            {{-- Completed --}}
            <div class="rounded-xl bg-blue-50 p-4">

                <div class="flex items-center justify-between">

                    <p class="text-sm font-medium text-blue-700">
                        Selesai
                    </p>

                    <span class="text-2xl font-bold text-blue-700">
                        {{ $groupStatus['completed'] ?? 0 }}
                    </span>

                </div>

            </div>


            {{-- Cancelled --}}
            <div class="rounded-xl bg-red-50 p-4">

                <div class="flex items-center justify-between">

                    <p class="text-sm font-medium text-red-700">
                        Dibatalkan
                    </p>

                    <span class="text-2xl font-bold text-red-700">
                        {{ $groupStatus['cancelled'] ?? 0 }}
                    </span>

                </div>

            </div>

        </div>

    </div>

@endsection
