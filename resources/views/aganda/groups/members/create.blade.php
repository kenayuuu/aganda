@extends('layouts.app')

@section('title', 'Tambah Anggota Group')
@section('page-title', 'Tambah Anggota')

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

        <a href="{{ route('aganda.groups.show', $group->id) }}" class="text-slate-400 transition hover:text-red-600">
            {{ $group->kode_group }}
        </a>

        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>

        <span class="font-medium text-slate-700">
            Tambah Anggota
        </span>

    </div>


    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Tambah Anggota Group
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Tambahkan calon atau jamaah ke dalam group AGANDA.
            </p>

        </div>


        <a href="{{ route('aganda.groups.show', $group->id) }}"
            class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7 7" />
            </svg>

            Kembali

        </a>

    </div>


    <div class="grid gap-6 lg:grid-cols-3">


        {{-- Informasi Group --}}
        <div class="lg:col-span-1">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

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

                            <p class="mt-0.5 text-xs text-slate-500">
                                Group tujuan anggota
                            </p>

                        </div>

                    </div>

                </div>


                <div class="space-y-5 px-6 py-6">

                    {{-- Kode Group --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Kode Group
                        </p>

                        <div class="mt-2 flex items-center gap-2">

                            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-bold text-slate-800">
                                {{ $group->kode_group }}
                            </span>

                        </div>

                    </div>


                    {{-- Owner --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Owner
                        </p>

                        <div class="mt-2 flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-sm font-bold text-red-600">
                                {{ strtoupper(substr($group->owner->name ?? 'U', 0, 1)) }}
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $group->owner->name ?? '-' }}
                                </p>

                                @if ($group->owner?->member_id)
                                    <p class="text-xs text-slate-400">
                                        {{ $group->owner->member_id }}
                                    </p>
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Paket --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Paket Kegiatan
                        </p>

                        <p class="mt-2 text-sm font-semibold text-slate-900">
                            {{ $group->packageKegiatan->nama_paket ?? '-' }}
                        </p>

                        @if ($group->packageKegiatan?->tanggal_berlangsung)
                            <p class="mt-1 text-xs text-slate-400">

                                {{ \Carbon\Carbon::parse($group->packageKegiatan->tanggal_berlangsung)->translatedFormat('d F Y') }}

                            </p>
                        @endif

                    </div>


                    {{-- Status --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status Group
                        </p>

                        <div class="mt-2">

                            @if ($group->status === 'active')
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @elseif ($group->status === 'completed')
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    Completed
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                    Cancelled
                                </span>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Form --}}
        <div class="lg:col-span-2">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}
                <div class="border-b border-slate-100 px-6 py-5">

                    <h2 class="font-semibold text-slate-900">
                        Data Anggota
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih calon atau jamaah yang akan ditambahkan ke group.
                    </p>

                </div>


                <form action="{{ route('aganda.groups.members.store', $group->id) }}" method="POST">

                    @csrf

                    <div class="space-y-6 px-6 py-6">


                        {{-- Pilih Calon --}}
                        <div>

                            <label for="calon_id" class="mb-2 block text-sm font-semibold text-slate-700">

                                Calon / Jamaah

                                <span class="text-red-500">*</span>

                            </label>


                            <select name="calon_id" id="calon_id" required
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-700 outline-none transition
                                    @error('calon_id')
                                        border-red-300 bg-red-50 focus:border-red-500 focus:ring-2 focus:ring-red-100
                                    @else
                                        border-slate-200 bg-white focus:border-red-500 focus:ring-2 focus:ring-red-100
                                    @enderror">

                                <option value="">
                                    -- Pilih Calon / Jamaah --
                                </option>


                                @forelse ($calons as $calon)
                                    <option value="{{ $calon->id }}"
                                        {{ old('calon_id') == $calon->id ? 'selected' : '' }}>

                                        {{ $calon->nama_lengkap }}

                                        @if ($calon->no_telepon)
                                            — {{ $calon->no_telepon }}
                                        @endif

                                    </option>

                                @empty

                                    <option value="" disabled>
                                        Belum ada calon untuk paket ini
                                    </option>
                                @endforelse

                            </select>


                            @error('calon_id')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-red-600">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20.36h15.6a2 2 0 001.73-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                    </svg>

                                    {{ $message }}

                                </p>
                            @else
                                <p class="mt-2 text-xs text-slate-400">
                                    Hanya calon yang terdaftar pada paket kegiatan
                                    {{ $group->packageKegiatan->nama_paket ?? 'group ini' }}
                                    yang ditampilkan.
                                </p>
                            @enderror

                        </div>


                        {{-- Jika tidak ada calon --}}
                        @if ($calons->isEmpty())
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                                <div class="flex gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3m0 4h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20.36h15.6a2 2 0 001.73-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-amber-900">
                                            Belum ada calon tersedia
                                        </p>

                                        <p class="mt-1 text-sm leading-6 text-amber-700">
                                            Belum terdapat calon atau jamaah yang dapat
                                            ditambahkan ke group ini untuk paket kegiatan tersebut.
                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endif


                        {{-- Informasi --}}
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">

                            <div class="flex gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 110-18 9 9 0 010 18z" />
                                    </svg>

                                </div>


                                <div>

                                    <p class="text-sm font-semibold text-blue-900">
                                        Informasi
                                    </p>

                                    <ul class="mt-2 space-y-1.5 text-sm leading-6 text-blue-700">

                                        <li class="flex gap-2">
                                            <span>•</span>
                                            <span>
                                                Calon yang ditampilkan hanya yang
                                                terdaftar pada paket kegiatan group ini.
                                            </span>
                                        </li>

                                        <li class="flex gap-2">
                                            <span>•</span>
                                            <span>
                                                Pendaftar akan otomatis dicatat berdasarkan
                                                akun yang sedang login.
                                            </span>
                                        </li>

                                        <li class="flex gap-2">
                                            <span>•</span>
                                            <span>
                                                Jika calon belum memiliki akun AGANDA,
                                                sistem akan membuat akun member secara otomatis.
                                            </span>
                                        </li>

                                    </ul>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">

                        <a href="{{ route('aganda.groups.show', $group->id) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">

                            Batal

                        </a>


                        <button type="submit" @if ($calons->isEmpty()) disabled @endif
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition
                                @if ($calons->isEmpty()) cursor-not-allowed bg-slate-300
                                @else
                                    bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 @endif">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>

                            Tambah Anggota

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
