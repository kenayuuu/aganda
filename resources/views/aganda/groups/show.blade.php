@extends('layouts.app')

@section('title', 'Detail Group AGANDA')
@section('page-title', 'Detail Group')

@section('content')

    {{-- Alert akun baru --}}
    @if (session('account_info'))
        <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-5">

            <div class="flex items-start gap-4">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">

                    <div>
                        <h3 class="font-semibold text-blue-900">
                            Akun AGANDA berhasil dibuat
                        </h3>

                        <p class="mt-1 text-sm text-blue-700">
                            Akun untuk
                            <strong>{{ session('account_info.name') }}</strong>
                            telah berhasil dibuat.
                        </p>
                    </div>

                    {{-- Informasi akun --}}
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                        <div class="rounded-xl border border-blue-100 bg-white p-4">
                            <p class="text-xs font-medium text-slate-500">
                                Member ID
                            </p>

                            <p class="mt-1 font-semibold text-slate-900">
                                {{ session('account_info.member_id') }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-blue-100 bg-white p-4">
                            <p class="text-xs font-medium text-slate-500">
                                Email
                            </p>

                            <p class="mt-1 break-all font-semibold text-slate-900">
                                {{ session('account_info.email') ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-blue-100 bg-white p-4">
                            <p class="text-xs font-medium text-slate-500">
                                Password Sementara
                            </p>

                            <div class="mt-1 flex items-center gap-2">

                                <code class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-800">
                                    {{ session('account_info.password') }}
                                </code>

                            </div>
                        </div>

                    </div>

                    {{-- Keterangan + tombol download --}}
                    <div
                        class="mt-4 flex flex-col gap-4 border-t border-blue-200 pt-4 sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-xs leading-5 text-blue-700">
                            Simpan informasi login ini dan berikan kepada anggota.
                            Password sementara hanya ditampilkan setelah akun berhasil dibuat.
                        </p>

                        <button type="button" onclick="downloadLoginInfo()"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">

                            {{-- Download icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14" />
                            </svg>

                            Download Informasi Login
                        </button>

                    </div>

                </div>

            </div>

        </div>

        {{-- Script download --}}
        <script>
            function downloadLoginInfo() {

                const nama = @json(session('account_info.name'));
                const memberId = @json(session('account_info.member_id'));
                const email = @json(session('account_info.email') ?? '-');
                const password = @json(session('account_info.password'));

                const isi = `
                ========================================
                        INFORMASI LOGIN AGANDA
                ========================================

                Nama                : ${nama}
                Member ID           : ${memberId}
                Email               : ${email}
                Password Sementara  : ${password}

                ========================================
                Simpan informasi ini dengan aman.
                Password di atas adalah password sementara.
                ========================================
                `;

                const blob = new Blob([isi], {
                    type: 'text/plain;charset=utf-8'
                });

                const url = URL.createObjectURL(blob);

                const link = document.createElement('a');
                link.href = url;
                link.download = `Informasi-Login-${nama.replace(/[^a-zA-Z0-9]/g, '-')}.txt`;

                document.body.appendChild(link);
                link.click();

                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }
        </script>
    @endif


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
            {{ $group->kode_group }}
        </span>

    </div>


    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $group->kode_group }}
                </h1>

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

            <p class="mt-1 text-sm text-slate-500">
                Informasi group dan daftar anggota AGANDA
            </p>
        </div>


        <div class="flex items-center gap-2">

            <a href="{{ route('aganda.groups.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>

                Kembali
            </a>

            @if (auth()->id() === $group->owner_id)
                <button type="button" onclick="openDeleteModal()"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14" />
                    </svg>

                    Hapus Group
                </button>
            @endif

        </div>

    </div>


    {{-- Statistik --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        {{-- Total anggota --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Total Anggota
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $group->members->count() }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Anggota aktif --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Anggota Aktif
                    </p>

                    <p class="mt-2 text-2xl font-bold text-emerald-600">
                        {{ $group->members->where('status', 'active')->count() }}
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Paket --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div class="min-w-0">

                    <p class="text-sm font-medium text-slate-500">
                        Paket Kegiatan
                    </p>

                    <p class="mt-2 truncate text-sm font-bold text-slate-900">
                        {{ $group->packageKegiatan->nama_paket ?? '-' }}
                    </p>

                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-14V4m0 16v-2m8-6h-2M6 12H4m13.657-5.657l-1.414 1.414M7.757 17.243l-1.414 1.414m0-13.314l1.414 1.414m11.314 11.314l-1.414-1.414" />
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- Informasi Group --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">

            <div>
                <h2 class="font-semibold text-slate-900">
                    Informasi Group
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Detail informasi group AGANDA
                </p>
            </div>

        </div>


        <div class="grid gap-x-8 gap-y-6 px-6 py-6 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Kode --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Kode Group
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->kode_group }}
                </p>
            </div>


            {{-- Owner --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Owner
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->owner->name ?? '-' }}
                </p>

                @if ($group->owner)
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $group->owner->member_id }}
                    </p>
                @endif
            </div>


            {{-- Paket --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Paket Kegiatan
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->packageKegiatan->nama_paket ?? '-' }}
                </p>
            </div>


            {{-- Status --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Status Group
                </p>

                <div class="mt-2">

                    @if ($group->status === 'active')
                        <span
                            class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                            Active
                        </span>
                    @elseif ($group->status === 'completed')
                        <span
                            class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                            Completed
                        </span>
                    @else
                        <span
                            class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
                            Cancelled
                        </span>
                    @endif

                </div>
            </div>


            {{-- Tanggal --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Tanggal Kegiatan
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->packageKegiatan?->tanggal_berlangsung
                        ? \Carbon\Carbon::parse($group->packageKegiatan->tanggal_berlangsung)->translatedFormat('d F Y')
                        : '-' }}
                </p>
            </div>


            {{-- Harga --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Harga Paket
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->packageKegiatan?->harga ? 'Rp ' . number_format($group->packageKegiatan->harga, 0, ',', '.') : '-' }}
                </p>
            </div>


            {{-- Deposit --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Deposit
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->packageKegiatan?->deposit
                        ? 'Rp ' . number_format($group->packageKegiatan->deposit, 0, ',', '.')
                        : '-' }}
                </p>
            </div>


            {{-- Dibuat --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Group Dibuat
                </p>

                <p class="mt-2 font-semibold text-slate-900">
                    {{ $group->created_at?->translatedFormat('d F Y, H:i') }}
                </p>
            </div>

        </div>

    </div>


    {{-- Anggota Group --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div
            class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2 class="font-semibold text-slate-900">
                    Anggota Group
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Daftar anggota yang tergabung dalam group ini
                </p>

            </div>


            @if ($group->status === 'active' && in_array(auth()->user()->role, ['karyawan', 'member']))
                <a href="{{ route('aganda.groups.members.create', $group->id) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>

                    Tambah Anggota

                </a>
            @endif

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="border-b border-slate-100 bg-slate-50">

                    <tr>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            No
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nama Anggota
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            No. Telepon
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Direkrut Oleh
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($group->members as $member)
                        <tr class="transition hover:bg-slate-50">

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                                {{ $loop->iteration }}
                            </td>


                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-600">
                                        {{ strtoupper(substr($member->calon->nama_lengkap ?? 'U', 0, 1)) }}
                                    </div>

                                    <div>

                                        <p class="font-semibold text-slate-900">
                                            {{ $member->calon->nama_lengkap ?? '-' }}
                                        </p>

                                        @if ($member->calon?->email)
                                            <p class="mt-0.5 text-xs text-slate-400">
                                                {{ $member->calon->email }}
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </td>


                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                {{ $member->calon->no_telepon ?? '-' }}
                            </td>


                            <td class="px-6 py-4">

                                <p class="text-sm font-medium text-slate-700">
                                    {{ $member->registeredBy->name ?? '-' }}
                                </p>

                                @if ($member->registeredBy?->member_id)
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        {{ $member->registeredBy->member_id }}
                                    </p>
                                @endif

                            </td>


                            <td class="whitespace-nowrap px-6 py-4">

                                @if ($member->status === 'active')
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @elseif ($member->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Cancelled
                                    </span>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="px-6 py-12 text-center">

                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>

                                </div>

                                <p class="mt-4 font-semibold text-slate-700">
                                    Belum ada anggota
                                </p>

                                <p class="mt-1 text-sm text-slate-400">
                                    Belum ada anggota yang terdaftar dalam group ini.
                                </p>

                                @if ($group->status === 'active' && in_array(auth()->user()->role, ['karyawan', 'member']))
                                    <a href="{{ route('aganda.groups.members.create', $group->id) }}"
                                        class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>

                                        Tambah anggota pertama

                                    </a>
                                @endif

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div id="deleteModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm">

        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

            <div class="flex items-start gap-4">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 3h15.64a2 2 0 001.71-3l-7.82-13a2 2 0 00-3.42 0z" />

                    </svg>

                </div>

                <div class="min-w-0 flex-1">

                    <h3 class="text-lg font-bold text-slate-900">
                        Hapus Group?
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Apakah kamu yakin ingin menghapus group
                        <span class="font-semibold text-slate-700">
                            {{ $group->kode_group }}
                        </span>?
                    </p>

                    <p class="mt-2 text-sm leading-6 text-red-600">
                        Semua data anggota yang terdaftar pada group ini juga akan dihapus.
                        Data akun anggota tetap aman.
                    </p>

                </div>

            </div>

            <div class="mt-6 flex justify-end gap-3">

                <button type="button" onclick="closeDeleteModal()"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </button>

                <form action="{{ route('aganda.groups.destroy', $group->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14" />

                        </svg>

                        Ya, Hapus Group
                    </button>

                </form>

            </div>

        </div>

    </div>

    <script>
        function openDeleteModal() {
            const modal = document.getElementById('deleteModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('deleteModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });
    </script>

@endsection
