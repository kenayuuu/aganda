@php
    $user = $node['user'];
    $type = $node['type'] ?? 'member';
    $line = $node['line'] ?? null;
    $children = $node['children'] ?? [];

    $isOwner = $type === 'owner';
    $isUpline = $type === 'upline';

    $commission = (int) ($node['commission'] ?? 0);

    $pairId = $node['pair_id'] ?? null;
    $pairBonus = (int) ($node['pair_bonus'] ?? 0);
    $pairPosition = $node['pair_position'] ?? null;

    $isPaired = !empty($pairId);

    $pairable = $node['pairable'] ?? false;
    $adminClickable = $node['admin_clickable'] ?? false;
    $adminView = $node['admin_view'] ?? false;
    $adminUrl = $node['admin_url'] ?? null;

    if ($adminView && !$isOwner) {
        $adminClickable = true;
    }

    $recruiter = $node['recruiter'] ?? null;
    $hasAvatar = !empty($user->avatar);

    $isLineOne = !$isOwner && !$isUpline && (int) $line === 1;

    $childCount = count($children);
@endphp

<div class="relative flex flex-col items-center shrink-0 min-w-max px-1.5 sm:px-2" data-node-user="{{ $user->id }}"
    data-node-line="{{ $line ?? '' }}" data-node-type="{{ $type }}" data-pair-id="{{ $pairId ?? '' }}"
    data-pair-position="{{ $pairPosition ?? '' }}" data-pair-bonus="{{ $pairBonus }}">

    <!-- KARTU MEMBER / KETUA -->
    <div class="relative w-[185px] sm:w-[200px] shrink-0" data-member-card data-user-id="{{ $user->id }}"
        data-member-id="{{ $user->id }}" data-line="{{ $line ?? '' }}" data-pair-id="{{ $pairId ?? '' }}"
        data-pair-position="{{ $pairPosition ?? '' }}" data-pair-bonus="{{ $pairBonus }}"
        data-pairable="{{ $pairable ? '1' : '0' }}" data-paired="{{ $isPaired ? '1' : '0' }}"
        data-admin-clickable="{{ $adminClickable ? '1' : '0' }}" data-admin-url="{{ $adminUrl ?? '' }}">

        <!-- Badge 3JT di Pojok Kanan Atas Member Line 1 -->
        @if ($isLineOne)
            <div class="absolute -right-2 -top-2.5 z-30">
                <div
                    class="flex items-center gap-1 rounded-full border border-emerald-300 bg-white px-2 py-0.5 shadow-md">
                    <span
                        class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500 text-[7px] font-bold text-white">
                        Rp
                    </span>
                    <span class="text-[8.5px] font-extrabold text-emerald-700">
                        3JT
                    </span>
                </div>
            </div>
        @endif

        <!-- Body Kartu -->
        <div
            class="w-full rounded-xl border p-2.5 shadow-sm transition duration-200
            {{ $adminClickable
                ? 'cursor-pointer hover:-translate-y-1 hover:border-blue-400 hover:shadow-lg'
                : ($pairable && !$isPaired
                    ? 'cursor-pointer hover:-translate-y-1 hover:border-amber-400 hover:shadow-lg'
                    : 'hover:-translate-y-1 hover:shadow-lg') }}
            {{ $isOwner
                ? 'border-red-200 bg-gradient-to-br from-red-50 to-white'
                : ($isUpline
                    ? 'border-blue-200 bg-blue-50'
                    : 'border-slate-200 bg-white') }}">

            <div class="flex items-center gap-2">
                <!-- Avatar / Inisial -->
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg
                    {{ $isOwner
                        ? 'bg-red-600 text-white'
                        : ($isUpline
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-slate-100 text-slate-600') }}">
                    @if ($hasAvatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                            class="h-full w-full object-cover">
                    @else
                        <span class="text-xs font-extrabold">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </span>
                    @endif
                </div>

                <!-- Info User -->
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[11px] font-bold text-slate-900 leading-tight" title="{{ $user->name }}">
                        {{ $user->name }}
                    </p>
                    <p class="truncate text-[8.5px] text-slate-500 mt-0.5">
                        {{ $user->member_id }}
                    </p>
                </div>
            </div>

            <!-- Badges Status -->
            <div class="mt-2 flex flex-wrap items-center gap-1">
                @if ($isOwner)
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-red-100 px-1.5 py-0.5 text-[8.5px] font-bold text-red-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                        Ketua Group
                    </span>
                @elseif ($isUpline)
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-1.5 py-0.5 text-[8.5px] font-bold text-blue-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                        Upline
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-1.5 py-0.5 text-[8.5px] font-semibold text-slate-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                        Line {{ $line }}
                    </span>
                @endif

                @if ($isPaired)
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-1.5 py-0.5 text-[8.5px] font-bold text-amber-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                        Pasangan
                    </span>
                @endif
            </div>

            <!-- Informasi Direkrut Oleh -->
            @if (!$isOwner && !$isUpline && $recruiter)
                <p class="mt-1.5 truncate text-[8.5px] text-slate-400">
                    Direkrut oleh <span class="font-semibold text-slate-600">{{ $recruiter->name }}</span>
                </p>
            @endif

            <!-- Pesan Interaksi Admin / Pasangan -->
            @if ($adminClickable)
                <p class="mt-1.5 text-center text-[8.5px] font-semibold text-blue-600">
                    Klik untuk lihat struktur
                </p>
            @elseif ($pairable && !$isPaired && empty($node['admin_view']))
                <p class="mt-1.5 text-center text-[8.5px] font-semibold text-amber-600">
                    Klik untuk pilih pasangan
                </p>
            @endif
        </div>
    </div>

    <!-- STRUKTUR CABANG ANAK (CHILDREN TREE) -->
    @if ($childCount > 0)
        <!-- Garis Vertikal Utama Turun dari Parent -->
        <div class="h-6 w-px shrink-0 bg-slate-300"></div>

        <!-- Container Barisan Anak -->
        <div class="relative flex items-start justify-center gap-3 sm:gap-4"
            data-children-container="{{ $user->id }}">
            @foreach ($children as $index => $child)
                <div class="relative flex flex-col items-center shrink-0" data-child-node="{{ $child['user']->id }}">

                    <!-- Garis Penghubung Horizontal Dinamis (50% Split) -->
                    @if ($childCount > 1)
                        @if ($index > 0)
                            <div class="absolute top-0 left-0 right-1/2 h-px bg-slate-300"></div>
                        @endif
                        @if ($index < $childCount - 1)
                            <div class="absolute top-0 left-1/2 right-0 h-px bg-slate-300"></div>
                        @endif
                    @endif

                    <!-- Garis Vertikal Masuk ke Node Anak -->
                    <div class="relative flex flex-col items-center">
                        <div class="h-6 w-px shrink-0 bg-slate-300"></div>
                    </div>

                    <!-- Rekursif Node Anak -->
                    @include('aganda.structure.node', ['node' => $child])
                </div>
            @endforeach
        </div>
    @endif

</div>
