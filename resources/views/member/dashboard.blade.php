@extends('layouts.app')

@section('title', 'Dashboard Member')

@section('page-title', 'Dashboard')

@section('content')

    <div class="mx-auto max-w-7xl">

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-sm font-medium text-slate-500">
                    Dashboard Member
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-900">
                    Selamat datang, {{ auth()->user()->name }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola informasi akun dan aktivitas AGANDA kamu.
                </p>

            </div>

            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-sm font-bold text-red-600">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>

                    <p class="text-sm font-semibold text-slate-900">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Member ID: {{ auth()->user()->member_id }}
                    </p>

                </div>

            </div>

        </div>


        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19a4 4 0 10-6 0m3-8a4 4 0 100-8 4 4 0 000 8zm7 8a3 3 0 00-2.5-2.95M18 11a3 3 0 100-6" />
                        </svg>

                    </div>

                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Status Akun
                </p>

                <p class="mt-1 text-lg font-bold text-slate-900">
                    Member Aktif
                </p>

            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                    </div>

                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Sponsor
                </p>

                <p class="mt-1 truncate text-lg font-bold text-slate-900">
                    {{ auth()->user()->parent?->name ?? '-' }}
                </p>

            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>

                    </div>

                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Direct Downline
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ auth()->user()->children()->count() }}
                </p>

            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-2.5 0-4.5 1.12-4.5 2.5S9.5 13 12 13s4.5 1.12 4.5 2.5S14.5 18 12 18m0-12v12m4.5-9.5c0-1.38-2-2.5-4.5-2.5S7.5 7.12 7.5 8.5" />
                        </svg>

                    </div>

                </div>

                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Bonus
                </p>

                <p class="mt-1 text-lg font-bold text-slate-900">
                    Rp 0
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Belum ada bonus tercatat
                </p>

            </div>

        </div>


        <div class="mt-6 grid gap-6 lg:grid-cols-3">

            <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Informasi Member
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi akun kamu di sistem AGANDA.
                    </p>

                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama Lengkap
                        </p>

                        <p class="mt-2 font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Member ID
                        </p>

                        <p class="mt-2 font-semibold text-slate-900">
                            {{ auth()->user()->member_id }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Email
                        </p>

                        <p class="mt-2 font-semibold text-slate-900">
                            {{ auth()->user()->email ?? '-' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            No. Telepon
                        </p>

                        <p class="mt-2 font-semibold text-slate-900">
                            {{ auth()->user()->phone ?? '-' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Sponsor
                        </p>

                        <p class="mt-2 font-semibold text-slate-900">
                            {{ auth()->user()->parent?->name ?? '-' }}
                        </p>

                        @if (auth()->user()->parent?->member_id)
                            <p class="mt-1 text-xs text-slate-400">
                                {{ auth()->user()->parent->member_id }}
                            </p>
                        @endif

                    </div>

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Role
                        </p>

                        <span
                            class="mt-2 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold capitalize text-blue-600">
                            {{ auth()->user()->role }}
                        </span>

                    </div>

                </div>

            </div>


            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Jaringan Saya
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan jaringan yang kamu rekrut.
                    </p>

                </div>

                <div class="p-6">

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-900">
                                    Direct Downline
                                </p>

                                <p class="text-xs text-slate-400">
                                    Member yang kamu rekrut langsung
                                </p>

                            </div>

                        </div>

                        <p class="text-xl font-bold text-slate-900">
                            {{ auth()->user()->children()->count() }}
                        </p>

                    </div>

                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 p-5 text-center">

                        <div
                            class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-400">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>

                        </div>

                        <p class="mt-3 text-sm font-semibold text-slate-700">
                            Kembangkan jaringanmu
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-400">
                            Rekrut member baru untuk membangun jaringan AGANDA kamu.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50 p-5">

            <div class="flex items-start gap-3">

                <div class="mt-0.5 shrink-0 text-blue-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
                    </svg>

                </div>

                <div>

                    <p class="text-sm font-semibold text-blue-900">
                        Selamat datang di AGANDA
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Gunakan dashboard ini untuk memantau informasi akun,
                        sponsor, jaringan, serta perkembangan bonus kamu.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection
