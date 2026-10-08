
@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Detail Pencairan Bonus
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi lengkap pengajuan pencairan bonus member.
                </p>
            </div>

            <a href="{{ route('admin.bonus.withdrawals.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                Kembali
            </a>
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

        <div class="grid gap-6 lg:grid-cols-2">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Member
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Nama
                        </div>

                        <div class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $withdrawal->user->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Email
                        </div>

                        <div class="mt-1 text-sm text-slate-700">
                            {{ $withdrawal->user->email ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Nomor Telepon
                        </div>

                        <div class="mt-1 text-sm text-slate-700">
                            {{ $withdrawal->user->no_hp ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Tanggal Pengajuan
                        </div>

                        <div class="mt-1 text-sm text-slate-700">
                            {{ $withdrawal->requested_at?->format('d F Y, H:i') ?? '-' }}
                        </div>
                    </div>

                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Pencairan
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Nominal Pengajuan
                        </div>

                        <div class="mt-1 text-2xl font-bold text-slate-900">
                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Status
                        </div>

                        <div class="mt-2">
                            @if ($withdrawal->status === 'pending')
                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                    Menunggu Konfirmasi
                                </span>
                            @elseif ($withdrawal->status === 'approved')
                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600">
                                    Disetujui
                                </span>
                            @elseif ($withdrawal->status === 'paid')
                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                    Sudah Dibayar
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
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Diproses
                        </div>

                        <div class="mt-1 text-sm text-slate-700">
                            {{ $withdrawal->processed_at?->format('d F Y, H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Diproses Oleh
                        </div>

                        <div class="mt-1 text-sm text-slate-700">
                            {{ $withdrawal->processedBy->name ?? '-' }}
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">
                Status Paket Umroh
            </h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-3">

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Harga Paket
                    </div>

                    <div class="mt-2 text-lg font-bold text-slate-900">
                        Rp {{ number_format($packagePrice, 0, ',', '.') }}
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Total Dibayar
                    </div>

                    <div class="mt-2 text-lg font-bold text-emerald-600">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Sisa Paket
                    </div>

                    <div class="mt-2 text-lg font-bold {{ $remainingPackage > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                        Rp {{ number_format($remainingPackage, 0, ',', '.') }}
                    </div>
                </div>

            </div>

            <div class="mt-5">
                @if ($isPackagePaidOff)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                        <div class="text-sm font-bold text-emerald-700">
                            Paket sudah lunas
                        </div>

                        <div class="mt-1 text-xs text-emerald-600">
                            Member sudah memenuhi syarat untuk melakukan pencairan bonus.
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
                        <div class="text-sm font-bold text-amber-700">
                            Paket belum lunas
                        </div>

                        <div class="mt-1 text-xs text-amber-600">
                            Pengajuan pencairan tidak dapat dikonfirmasi sebelum paket member lunas.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">
                Bonus Member
            </h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-3">

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Total Bonus
                    </div>

                    <div class="mt-2 text-lg font-bold text-slate-900">
                        Rp {{ number_format($totalBonus, 0, ',', '.') }}
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Sudah Digunakan
                    </div>

                    <div class="mt-2 text-lg font-bold text-slate-900">
                        Rp {{ number_format($totalAllocated, 0, ',', '.') }}
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-50 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Tersedia
                    </div>

                    <div class="mt-2 text-lg font-bold text-emerald-600">
                        Rp {{ number_format($availableBonus, 0, ',', '.') }}
                    </div>
                </div>

            </div>
        </div>

        @if ($withdrawal->notes)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Catatan
                </h2>

                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $withdrawal->notes }}
                </p>
            </div>
        @endif

@if ($withdrawal->status === 'pending')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">
            Tindakan Admin
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            Pastikan bonus dan status paket sudah sesuai sebelum menerima pengajuan.
        </p>

        <div class="mt-5 flex flex-col gap-3 sm:flex-row">

            @php
                $userRole = $withdrawal->user->role ?? null;

                $withdrawalAllowed =
                    $availableBonus >= $withdrawal->amount
                    &&
                    (
                        $userRole === 'karyawan'
                        ||
                        (
                            $userRole === 'member'
                            && $isPackagePaidOff
                        )
                    );
            @endphp

            @if ($withdrawalAllowed)
                <form
                    action="{{ route('admin.bonus.withdrawals.approve', $withdrawal) }}"
                    method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menerima pengajuan pencairan ini?')"
                >
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 sm:w-auto"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                        Terima Pengajuan
                    </button>
                </form>
            @endif

            <form
                action="{{ route('admin.bonus.withdrawals.reject', $withdrawal) }}"
                method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan pencairan ini?')"
            >
                @csrf

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-red-700 sm:w-auto"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>

                    Tolak Pengajuan
                </button>
            </form>

        </div>

        @if ($userRole === 'member' && !$isPackagePaidOff)

            <div class="mt-4 text-xs font-medium text-amber-600">
                Tombol terima pengajuan tidak tersedia karena paket member belum lunas.
            </div>

        @elseif ($availableBonus < $withdrawal->amount)

            <div class="mt-4 text-xs font-medium text-red-600">
                Bonus tersedia tidak mencukupi untuk pengajuan ini.
            </div>

        @endif
    </div>
@endif


@if ($withdrawal->status === 'approved')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <h2 class="text-lg font-bold text-slate-900">
            Tindakan Admin
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            Pengajuan sudah diterima. Lakukan pembayaran kepada penerima, kemudian konfirmasi pembayaran.
        </p>

        <div class="mt-5">

            <form
                action="{{ route('admin.bonus.withdrawals.paid', $withdrawal) }}"
                method="POST"
                onsubmit="return confirm('Apakah bonus sudah benar-benar dibayarkan kepada penerima?')"
            >
                @csrf

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700 sm:w-auto"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>

                    Konfirmasi Dibayar
                </button>
            </form>

        </div>

    </div>
@endif


    </div>
@endsection
