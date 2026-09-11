@extends('layouts.app')

@section('title', 'Struktur Group')

@section('page-title', 'Struktur Group')

@section('content')

    <style>
        #structure-canvas {
            min-height: 500px;
        }

        #structure-canvas [data-member-card] {
            width: 205px !important;
            max-width: 205px !important;
        }

        #structure-canvas [data-member-card] .text-lg {
            font-size: 0.95rem !important;
        }

        #structure-canvas [data-member-card] .text-sm {
            font-size: 0.75rem !important;
        }

        #structure-canvas [data-member-card] .text-xs {
            font-size: 0.68rem !important;
        }

        #structure-canvas [data-member-card] .p-6 {
            padding: 0.85rem !important;
        }

        #structure-canvas [data-member-card] .p-5 {
            padding: 0.75rem !important;
        }

        #structure-canvas [data-member-card] .p-4 {
            padding: 0.65rem !important;
        }

        #structure-canvas [data-member-card] .h-16,
        #structure-canvas [data-member-card] .w-16 {
            height: 2.75rem !important;
            width: 2.75rem !important;
        }

        #structure-canvas [data-member-card] .h-14,
        #structure-canvas [data-member-card] .w-14 {
            height: 2.5rem !important;
            width: 2.5rem !important;
        }

        #structure-canvas [data-member-card] {
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        #structure-canvas [data-member-card].pair-selectable {
            cursor: pointer;
        }

        #structure-canvas [data-member-card].pair-selectable:hover {
            transform: translateY(-3px) scale(1.015);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
        }

        #structure-canvas [data-member-card].pair-selected {
            border-color: #f59e0b !important;
            box-shadow:
                0 0 0 3px rgba(245, 158, 11, 0.18),
                0 12px 28px rgba(245, 158, 11, 0.15);
            transform: translateY(-3px);
        }

        #structure-canvas [data-member-card].pair-disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        #structure-canvas [data-member-card] .pair-selected-badge {
            display: none;
        }

        #structure-canvas [data-member-card].pair-selected .pair-selected-badge {
            display: inline-flex;
        }

        @media (max-width: 768px) {
            #structure-canvas [data-member-card] {
                width: 180px !important;
                max-width: 180px !important;
            }
        }
    </style>

    <div class="mx-auto max-w-7xl">

        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">

            <a href="{{ route('aganda.groups.index') }}" class="transition hover:text-red-600">
                Group
            </a>

            <span>/</span>

            <a href="{{ route('aganda.groups.show', $group->id) }}" class="transition hover:text-red-600">
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
                            Struktur anggota group {{ $group->kode_group }}.
                        </p>

                    </div>

                </div>

            </div>

            <a href="{{ route('aganda.groups.show', $group->id) }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />

                </svg>

                Kembali ke Group

            </a>

        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Kode Group
                </p>

                <p class="mt-2 text-lg font-bold text-slate-900">
                    {{ $group->kode_group }}
                </p>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Owner Group
                </p>

                <p class="mt-2 text-lg font-bold text-slate-900">
                    {{ $group->owner->name ?? '-' }}
                </p>

                @if ($group->owner)
                    <p class="mt-1 text-xs text-slate-400">
                        {{ $group->owner->member_id }}
                    </p>
                @endif

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Total Anggota Aktif
                </p>

                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $group->members->where('status', 'active')->count() }}
                </p>

            </div>

        </div>

        @if (session('success'))
            <div
                class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">

                {{ session('success') }}

            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">

                {{ session('error') }}

            </div>
        @endif

        @if ($errors->any())

            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">

                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach

            </div>

        @endif

        @if (auth()->user()->role !== 'admin')
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">

                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                    <div>

                        <h2 class="text-lg font-bold text-amber-900">
                            Pairing Line 2
                        </h2>

                        <p class="mt-1 text-sm text-amber-700">
                            Pilih dua anggota yang berada tepat di Line 2 Anda untuk mendapatkan bonus Rp500.000.
                        </p>

                    </div>

                    <div class="flex items-center gap-3">

                        <span id="pair-count"
                            class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm">
                            0 / 2 dipilih
                        </span>

                        <form id="pair-form" action="{{ route('aganda.groups.pair', $group->id) }}" method="POST">

                            @csrf

                            <input type="hidden" name="left_member_id" id="left_member_id">

                            <input type="hidden" name="right_member_id" id="right_member_id">

                            <button type="submit" id="pair-button" disabled
                                class="rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-white opacity-50 transition hover:bg-amber-600 disabled:cursor-not-allowed">

                                Pasangkan

                            </button>

                        </form>

                    </div>

                </div>

                <div class="mt-4 rounded-xl border border-amber-200 bg-white p-4">

                    <p class="text-xs leading-5 text-slate-500">
                        Anggota yang dapat dipasangkan dapat dipilih langsung pada kartu struktur.
                        Hanya anggota yang berada tepat dua tingkat di bawah akun Anda yang dapat dipasangkan.
                    </p>

                </div>

            </div>
        @endif

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                    <div>

                        <h2 class="text-lg font-bold text-slate-900">
                            Struktur Anggota
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Struktur anggota berdasarkan pihak yang mendaftarkan anggota dalam group.
                        </p>

                    </div>

                    <div class="flex flex-wrap items-center gap-3">

                        @if ($group->status === 'active')
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">

                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                Aktif

                            </span>
                        @elseif ($group->status === 'completed')
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">

                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>

                                Selesai

                            </span>
                        @elseif ($group->status === 'cancelled')
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700">

                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                Dibatalkan

                            </span>
                        @endif

                    </div>

                </div>

            </div>

            <div class="border-b border-slate-100 bg-slate-50 px-6 py-3">

                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-slate-500">

                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-red-500"></span>

                        <span>
                            Owner
                        </span>

                    </div>

                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>

                        <span>
                            Member
                        </span>

                    </div>

                    <div class="flex items-center gap-2">

                        <span class="h-2 w-2 rounded-full bg-blue-400"></span>

                        <span>
                            Upline
                        </span>

                    </div>

                </div>

            </div>

            <div class="overflow-x-auto">

                <div id="structure-canvas" class="relative min-w-max px-6 py-8">

                    <svg id="pair-lines" class="pointer-events-none absolute left-0 top-0 z-10"
                        style="overflow: visible;"></svg>

                    <div class="relative z-20 flex justify-center">

                        @include('aganda.structure.node', [
                            'node' => $tree,
                        ])

                    </div>

                </div>

            </div>

        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">

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
                        Informasi Struktur
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Bagan menampilkan seluruh anggota aktif pada group ini.
                        Hubungan antaranggota ditentukan berdasarkan pihak yang
                        mendaftarkan anggota tersebut.
                    </p>

                </div>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('structure-canvas');
            const svg = document.getElementById('pair-lines');
            const pairForm = document.getElementById('pair-form');
            const pairButton = document.getElementById('pair-button');
            const pairCount = document.getElementById('pair-count');
            const leftInput = document.getElementById('left_member_id');
            const rightInput = document.getElementById('right_member_id');

            if (!canvas || !svg) {
                return;
            }

            const selectedMembers = [];

            function createSvgElement(tag) {
                return document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    tag
                );
            }

            function updatePairForm() {
                if (!pairCount || !pairButton || !leftInput || !rightInput) {
                    return;
                }

                pairCount.textContent =
                    selectedMembers.length + ' / 2 dipilih';

                leftInput.value =
                    selectedMembers[0] || '';

                rightInput.value =
                    selectedMembers[1] || '';

                if (selectedMembers.length === 2) {
                    pairButton.disabled = false;
                    pairButton.classList.remove('opacity-50');
                    pairButton.classList.add('opacity-100');
                } else {
                    pairButton.disabled = true;
                    pairButton.classList.add('opacity-50');
                    pairButton.classList.remove('opacity-100');
                }

                document.querySelectorAll(
                    '[data-member-card][data-pair-selectable]'
                ).forEach(function(card) {
                    const memberId = String(
                        card.dataset.memberId || ''
                    );

                    if (
                        selectedMembers.length >= 2 &&
                        !selectedMembers.includes(memberId)
                    ) {
                        card.classList.add('pair-disabled');
                    } else {
                        card.classList.remove('pair-disabled');
                    }
                });
            }

            function getMemberId(card) {
                return String(
                    card.dataset.memberId ||
                    card.dataset.userId ||
                    card.dataset.id ||
                    ''
                );
            }

            function isLineTwoCard(card) {
                if (
                    card.dataset.line === '2' ||
                    card.dataset.level === '2' ||
                    card.dataset.depth === '2'
                ) {
                    return true;
                }

                const text = card.textContent || '';

                return /Line\s*2/i.test(text);
            }

            function setupPairSelection() {
                const cards = Array.from(
                    canvas.querySelectorAll('[data-member-card]')
                );

                cards.forEach(function(card) {
                    const memberId = getMemberId(card);

                    if (!memberId) {
                        return;
                    }

                    if (!isLineTwoCard(card)) {
                        return;
                    }

                    card.dataset.pairSelectable = '1';
                    card.dataset.memberId = memberId;
                    card.classList.add('pair-selectable');

                    card.addEventListener('click', function(event) {
                        if (event.target.closest('a')) {
                            return;
                        }

                        if (
                            event.target.closest('button') &&
                            !card.classList.contains('pair-selectable')
                        ) {
                            return;
                        }

                        const currentId = String(
                            card.dataset.memberId || ''
                        );

                        if (!currentId) {
                            return;
                        }

                        const existingIndex =
                            selectedMembers.indexOf(currentId);

                        if (existingIndex !== -1) {
                            selectedMembers.splice(
                                existingIndex,
                                1
                            );

                            card.classList.remove(
                                'pair-selected'
                            );

                            updatePairForm();

                            return;
                        }

                        if (selectedMembers.length >= 2) {
                            return;
                        }

                        selectedMembers.push(currentId);

                        card.classList.add(
                            'pair-selected'
                        );

                        updatePairForm();
                    });
                });

                updatePairForm();
            }

            function drawPairLines() {
                svg.innerHTML = '';

                const canvasRect =
                    canvas.getBoundingClientRect();

                svg.setAttribute(
                    'width',
                    canvas.scrollWidth
                );

                svg.setAttribute(
                    'height',
                    canvas.scrollHeight
                );

                const cards = Array.from(
                    canvas.querySelectorAll(
                        '[data-member-card][data-pair-id]'
                    )
                );

                const pairs = {};

                cards.forEach(function(card) {
                    const pairId =
                        card.dataset.pairId;

                    if (!pairId) {
                        return;
                    }

                    if (!pairs[pairId]) {
                        pairs[pairId] = [];
                    }

                    pairs[pairId].push(card);
                });

                Object.values(pairs).forEach(function(pairCards) {
                    if (pairCards.length !== 2) {
                        return;
                    }

                    const firstCard =
                        pairCards[0];

                    const secondCard =
                        pairCards[1];

                    const firstRect =
                        firstCard.getBoundingClientRect();

                    const secondRect =
                        secondCard.getBoundingClientRect();

                    const x1 =
                        firstRect.left -
                        canvasRect.left +
                        canvas.scrollLeft +
                        firstRect.width / 2;

                    const x2 =
                        secondRect.left -
                        canvasRect.left +
                        canvas.scrollLeft +
                        secondRect.width / 2;

                    const y1 =
                        firstRect.bottom -
                        canvasRect.top +
                        canvas.scrollTop;

                    const y2 =
                        secondRect.bottom -
                        canvasRect.top +
                        canvas.scrollTop;

                    const lineY =
                        Math.max(y1, y2) + 28;

                    const middleX =
                        (x1 + x2) / 2;

                    const path =
                        createSvgElement('path');

                    path.setAttribute(
                        'd',
                        `
                            M ${x1} ${y1}
                            L ${x1} ${lineY}
                            L ${x2} ${lineY}
                            L ${x2} ${y2}
                        `
                    );

                    path.setAttribute(
                        'fill',
                        'none'
                    );

                    path.setAttribute(
                        'stroke',
                        '#f59e0b'
                    );

                    path.setAttribute(
                        'stroke-width',
                        '2'
                    );

                    path.setAttribute(
                        'stroke-linecap',
                        'round'
                    );

                    path.setAttribute(
                        'stroke-linejoin',
                        'round'
                    );

                    svg.appendChild(path);

                    const pairBonus =
                        Number(
                            firstCard.dataset.pairBonus ||
                            secondCard.dataset.pairBonus ||
                            0
                        );

                    if (pairBonus <= 0) {
                        return;
                    }

                    const labelWidth = 100;
                    const labelHeight = 30;

                    const labelX =
                        middleX -
                        labelWidth / 2;

                    const labelY =
                        lineY -
                        labelHeight / 2;

                    const labelGroup =
                        createSvgElement('g');

                    const background =
                        createSvgElement('rect');

                    background.setAttribute(
                        'x',
                        labelX
                    );

                    background.setAttribute(
                        'y',
                        labelY
                    );

                    background.setAttribute(
                        'width',
                        labelWidth
                    );

                    background.setAttribute(
                        'height',
                        labelHeight
                    );

                    background.setAttribute(
                        'rx',
                        '15'
                    );

                    background.setAttribute(
                        'fill',
                        '#ffffff'
                    );

                    background.setAttribute(
                        'stroke',
                        '#fbbf24'
                    );

                    background.setAttribute(
                        'stroke-width',
                        '1.5'
                    );

                    background.setAttribute(
                        'filter',
                        'drop-shadow(0 2px 3px rgba(0,0,0,0.08))'
                    );

                    const iconCircle =
                        createSvgElement('circle');

                    iconCircle.setAttribute(
                        'cx',
                        middleX - 27
                    );

                    iconCircle.setAttribute(
                        'cy',
                        lineY
                    );

                    iconCircle.setAttribute(
                        'r',
                        '8'
                    );

                    iconCircle.setAttribute(
                        'fill',
                        '#f59e0b'
                    );

                    const iconText =
                        createSvgElement('text');

                    iconText.setAttribute(
                        'x',
                        middleX - 27
                    );

                    iconText.setAttribute(
                        'y',
                        lineY + 3
                    );

                    iconText.setAttribute(
                        'text-anchor',
                        'middle'
                    );

                    iconText.setAttribute(
                        'font-size',
                        '7'
                    );

                    iconText.setAttribute(
                        'font-weight',
                        '800'
                    );

                    iconText.setAttribute(
                        'fill',
                        '#ffffff'
                    );

                    iconText.textContent = 'Rp';

                    const text =
                        createSvgElement('text');

                    text.setAttribute(
                        'x',
                        middleX + 8
                    );

                    text.setAttribute(
                        'y',
                        lineY + 4
                    );

                    text.setAttribute(
                        'text-anchor',
                        'middle'
                    );

                    text.setAttribute(
                        'font-size',
                        '10'
                    );

                    text.setAttribute(
                        'font-weight',
                        '800'
                    );

                    text.setAttribute(
                        'fill',
                        '#b45309'
                    );

                    text.textContent =
                        Number(
                            pairBonus
                        ).toLocaleString('id-ID');

                    labelGroup.appendChild(
                        background
                    );

                    labelGroup.appendChild(
                        iconCircle
                    );

                    labelGroup.appendChild(
                        iconText
                    );

                    labelGroup.appendChild(
                        text
                    );

                    svg.appendChild(
                        labelGroup
                    );
                });
            }

            document.querySelectorAll(
                '[data-member-card][data-admin-clickable="1"]'
            ).forEach(function(card) {
                card.addEventListener(
                    'click',
                    function(event) {
                        if (event.target.closest('a')) {
                            return;
                        }

                        const url =
                            card.dataset.adminUrl;

                        if (!url) {
                            return;
                        }

                        window.location.href = url;
                    }
                );
            });

            setupPairSelection();

            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    drawPairLines();
                });
            });

            window.addEventListener(
                'resize',
                function() {
                    requestAnimationFrame(
                        drawPairLines
                    );
                }
            );

            canvas.addEventListener(
                'scroll',
                function() {
                    requestAnimationFrame(
                        drawPairLines
                    );
                }
            );

            if (window.ResizeObserver) {
                const observer =
                    new ResizeObserver(function() {
                        requestAnimationFrame(
                            drawPairLines
                        );
                    });

                observer.observe(canvas);
            }

            if (pairForm) {
                pairForm.addEventListener(
                    'submit',
                    function(event) {
                        if (selectedMembers.length !== 2) {
                            event.preventDefault();
                            return;
                        }

                        leftInput.value =
                            selectedMembers[0];

                        rightInput.value =
                            selectedMembers[1];
                    }
                );
            }
        });
    </script>

@endsection
