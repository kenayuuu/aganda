@extends('layouts.app')

@section('content')
    <div class="space-y-4">

        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Semua Member
            </h1>

            <p class="mt-1 text-xs text-slate-500">
                Daftar seluruh member yang terdaftar dalam sistem AGANDA.
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

            <form method="GET" action="{{ route('admin.members.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-4">

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Cari Member
                    </label>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, Member ID, atau email..."
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Paket Kegiatan
                    </label>

                    <select name="package"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">

                        <option value="">
                            Semua Paket
                        </option>

                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}" {{ request('package') == $package->id ? 'selected' : '' }}>
                                {{ $package->nama_paket }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Sponsor
                    </label>

                    <select name="sponsor"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">

                        <option value="">
                            Semua Sponsor
                        </option>

                        @foreach ($sponsors as $sponsor)
                            <option value="{{ $sponsor->id }}" {{ request('sponsor') == $sponsor->id ? 'selected' : '' }}>
                                {{ $sponsor->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="flex items-end gap-2 md:col-span-4">

                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                        Filter
                    </button>

                    <a href="{{ route('admin.members.index') }}"
                        class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                        Reset
                    </a>

                </div>

            </form>

        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-2 border-b border-slate-100 px-5 py-3.5 md:flex-row md:items-center md:justify-between">

                <div>
                    <h2 class="text-sm font-bold text-slate-900">
                        Daftar Member
                    </h2>

                    <p class="mt-0.5 text-[10px] text-slate-500">
                        Menampilkan
                        {{ $members->firstItem() ?? 0 }}
                        sampai
                        {{ $members->lastItem() ?? 0 }}
                        dari
                        {{ $members->total() }}
                        member
                    </p>
                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[850px] text-left">

                    <thead class="bg-slate-50">

                        <tr class="border-b border-slate-100">

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                No
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Member
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Member ID
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Email
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Paket Kegiatan
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Sponsor
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                Terdaftar
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($members as $index => $member)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-4 py-2.5 text-[11px] text-slate-500">
                                    {{ $members->firstItem() + $index }}
                                </td>

                                <td class="px-4 py-2.5">

                                    <div class="flex items-center gap-2.5">

                                        @if ($member->avatar)
                                            <img src="{{ asset('storage/' . $member->avatar) }}" alt="{{ $member->name }}"
                                                class="h-8 w-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-50 text-xs font-bold text-red-600">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif

                                        <div class="min-w-0">

                                            <p class="truncate text-xs font-semibold text-slate-800">
                                                {{ $member->name }}
                                            </p>

                                            <p class="text-[9px] text-slate-400">
                                                Member
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td class="px-4 py-2.5 text-[11px] font-medium text-slate-700">
                                    {{ $member->member_id ?? '-' }}
                                </td>

                                <td class="px-4 py-2.5 text-[11px] text-slate-600">
                                    {{ $member->email }}
                                </td>

                                <td class="px-4 py-2.5 text-[11px] text-slate-600">
                                    {{ $member->calon?->packageKegiatan?->nama_paket ?? '-' }}
                                </td>

                                <td class="px-4 py-2.5">

                                    @if ($member->parent)
                                        <div>

                                            <p class="text-[11px] font-medium text-slate-700">
                                                {{ $member->parent->name }}
                                            </p>

                                            <p class="text-[9px] text-slate-400">
                                                {{ $member->parent->member_id ?? '-' }}
                                            </p>

                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400">
                                            -
                                        </span>
                                    @endif

                                </td>

                                <td class="px-4 py-2.5">

                                    <span
                                        class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-semibold text-emerald-600">
                                        Aktif
                                    </span>

                                </td>

                                <td class="px-4 py-2.5 text-[11px] text-slate-500">
                                    {{ $member->created_at?->format('d M Y') }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="px-5 py-10 text-center">

                                    <p class="text-xs font-semibold text-slate-600">
                                        Tidak ada member ditemukan
                                    </p>

                                    <p class="mt-1 text-[10px] text-slate-400">
                                        Coba ubah filter atau kata pencarian.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($members->hasPages())
                <div class="border-t border-slate-100 px-5 py-3">

                    {{ $members->links() }}

                </div>
            @endif

        </div>

    </div>
@endsection
