@extends('layouts.app')

@section('title', 'Struktur Group')

@section('page-title', 'Struktur Group')

@section('content')

    <div class="mx-auto max-w-7xl">

        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">

            <a href="{{ route('aganda.groups.index') }}" class="transition hover:text-red-600">
                Group
            </a>

            <span>/</span>

            <a href="{{ route('aganda.groups.show', $group) }}" class="transition hover:text-red-600">
                {{ $group->kode_group }}
            </a>

            <span>/</span>

            <span class="font-medium text-slate-700">
                Struktur
            </span>

        </div>

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <div class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M5 8l7-5 7 5M5 16l7 5 7-5" />

                        </svg>

                    </div>

                    <div>

                        <h1 class="text-2xl font-bold text-slate-900">
                            Struktur Group
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Struktur anggota group {{ $group->kode_group }}
                        </p>

                    </div>

                </div>

            </div>

            <a href="{{ route('aganda.groups.show', $group) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />

                </svg>

                Kembali ke Group

            </a>

        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Kode Group
                </p>

                <p class="mt-2 font-bold text-slate-900">
                    {{ $group->kode_group }}
                </p>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Owner Group
                </p>

                <p class="mt-2 font-bold text-slate-900">
                    {{ $group->owner->name }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    {{ $group->owner->member_id }}
                </p>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Total Anggota
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $group->members->where('status', 'active')->count() }}
                </p>

            </div>

        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Bagan Struktur Group
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Seluruh anggota yang terdaftar dalam group ditampilkan di bawah owner group.
                        </p>

                    </div>

                    <div class="hidden items-center gap-4 text-xs text-slate-400 sm:flex">

                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Owner
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                            Member
                        </div>

                    </div>

                </div>

            </div>

            <div class="overflow-x-auto">

                <div class="min-w-max px-6 py-8">

                    <div class="flex justify-center">

                        @include('aganda.structure.node', [
                            'node' => $tree,
                        ])

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
                        Struktur diperbarui otomatis
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Bagan menampilkan seluruh anggota aktif yang terdaftar dalam group.
                        Informasi mengenai pihak yang merekrut anggota ditampilkan pada masing-masing kartu anggota.
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection
