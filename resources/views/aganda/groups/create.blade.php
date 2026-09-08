@extends('layouts.app')

@section('title', 'Buat Group AGANDA')
@section('page-title', 'Buat Group')

@section('content')

    {{-- Breadcrumb --}}
    <div class="mb-5 flex items-center gap-2 text-sm">

        <a href="{{ route('admin.dashboard') }}" class="text-slate-400 transition hover:text-red-600">
            Dashboard
        </a>

        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>

        <a href="{{ route('aganda.groups.index') }}" class="text-slate-400 transition hover:text-red-600">
            Semua Group
        </a>

        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>

        <span class="font-medium text-slate-700">
            Buat Group
        </span>

    </div>


    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Buat Group AGANDA
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Buat group baru untuk kegiatan perjalanan.
            </p>
        </div>

        <a href="{{ route('aganda.groups.index') }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>

            Kembali
        </a>

    </div>


    {{-- Form --}}
    <div class="max-w-3xl">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Card Header --}}
            <div class="border-b border-slate-100 px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Informasi Group
                        </h2>

                        <p class="mt-0.5 text-sm text-slate-500">
                            Tentukan pemilik dan paket kegiatan untuk group baru.
                        </p>

                    </div>

                </div>

            </div>


            <form action="{{ route('aganda.groups.store') }}" method="POST">

                @csrf

                <div class="space-y-6 px-6 py-6">


                    {{-- Pemilik Group --}}
                    <div>

                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Pemilik Group
                        </label>

                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 font-bold text-red-600">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>

                            <div class="min-w-0">

                                <p class="font-semibold text-slate-900">
                                    {{ auth()->user()->name }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ auth()->user()->member_id }}
                                    <span class="mx-1">•</span>
                                    {{ ucfirst(auth()->user()->role) }}
                                </p>

                            </div>

                        </div>

                        <p class="mt-2 text-xs text-slate-400">
                            Pemilik group otomatis adalah akun yang sedang login.
                        </p>

                    </div>


                    {{-- Paket Kegiatan --}}
                    <div>

                        <label for="package_kegiatan_id" class="mb-2 block text-sm font-semibold text-slate-700">

                            Paket Kegiatan

                            <span class="text-red-500">*</span>

                        </label>

                        <select name="package_kegiatan_id" id="package_kegiatan_id" required
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-700 outline-none transition
                                @error('package_kegiatan_id')
                                    border-red-300 bg-red-50 focus:border-red-500 focus:ring-2 focus:ring-red-100
                                @else
                                    border-slate-200 bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100
                                @enderror">

                            <option value="">
                                -- Pilih Paket Kegiatan --
                            </option>

                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ old('package_kegiatan_id') == $package->id ? 'selected' : '' }}>

                                    {{ $package->nama_paket }}

                                    @if ($package->tanggal_berlangsung)
                                        — {{ \Carbon\Carbon::parse($package->tanggal_berlangsung)->format('d M Y') }}
                                    @endif

                                </option>
                            @endforeach

                        </select>


                        @error('package_kegiatan_id')
                            <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-600">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20.36h15.6a2 2 0 001.73-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>

                                {{ $message }}

                            </p>
                        @else
                            <p class="mt-2 text-xs text-slate-400">
                                Pilih paket perjalanan yang akan digunakan oleh group ini.
                            </p>
                        @enderror

                    </div>


                    {{-- Info --}}
                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">

                        <div class="flex gap-3">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 110-18 9 9 0 010 18z" />
                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-blue-900">
                                    Informasi Group
                                </p>

                                <p class="mt-1 text-sm leading-6 text-blue-700">
                                    Kode group akan dibuat secara otomatis oleh sistem
                                    setelah group berhasil dibuat.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Footer --}}
                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">

                    <a href="{{ route('aganda.groups.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">

                        Batal

                    </a>


                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>

                        Buat Group

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
