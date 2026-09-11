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
            <span class="font-medium text-slate-700">
                Struktur Group
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
                            Menampilkan struktur seluruh group yang terdaftar dalam sistem.
                        </p>
                    </div>
                </div>
            </div>

            <a href="{{ route('aganda.groups.index') }}"
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
                    Total Group
                </p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $structures->count() }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Group Aktif
                </p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ $structures->where('group.status', 'active')->count() }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Group Selesai
                </p>
                <p class="mt-2 text-2xl font-bold text-blue-600">
                    {{ $structures->where('group.status', 'completed')->count() }}
                </p>
            </div>
        </div>

        @forelse ($structures as $structure)
            @php
                $group = $structure['group'];
                $tree = $structure['tree'];
            @endphp

            <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="font-bold text-slate-900">
                                {{ $group->kode_group }}
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $group->packageKegiatan->nama_paket ?? '-' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                Owner:
                                <span class="font-semibold text-slate-600">
                                    {{ $group->owner->name ?? '-' }}
                                </span>
                            </p>
                        </div>

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

                <div class="border-b border-slate-100 bg-slate-50 px-6 py-3">
                    <div class="flex flex-wrap items-center gap-5 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Owner
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                            Member
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                            Upline
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                            Pasangan
                        </div>
                    </div>
                </div>

                <!-- TREE CONTAINER DENGAN SVG OVERLAY UNTUK GARIS PASANGAN -->
                <div class="relative overflow-x-auto tree-wrapper">
                    <!-- Layer Canvas SVG Overlay -->
                    <svg class="pair-svg-overlay pointer-events-none absolute inset-0 z-20 overflow-visible"></svg>

                    <div class="min-w-max px-8 py-10">
                        <div class="flex justify-center">
                            @include('aganda.structure.node', [
                                'node' => $tree,
                            ])
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <p class="text-sm font-semibold text-slate-700">
                    Belum ada group
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Belum terdapat group yang dapat ditampilkan.
                </p>
            </div>
        @endforelse

        <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50 p-5">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 shrink-0 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000-18 9 9 0 000 18z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-900">
                        Informasi Struktur
                    </p>
                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        Struktur anggota pada setiap group ditampilkan berdasarkan pihak yang mendaftarkan anggota tersebut.
                        Garis oranye menandakan hubungan antar pasangan dengan bonus Rp 500.000.
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handler klik kartu untuk Admin
            document.querySelectorAll(
                '[data-member-card][data-admin-clickable="1"]'
            ).forEach(function(card) {
                card.addEventListener('click', function() {
                    const url = card.dataset.adminUrl;
                    if (!url) return;
                    window.location.href = url;
                });
            });

            // FUNGSI MENGGAMBAR GARIS PASANGAN SIKU & BADGE RP 500.000
            function drawPairLines() {
                document.querySelectorAll('.tree-wrapper').forEach(function(wrapper) {
                    const svg = wrapper.querySelector('.pair-svg-overlay');
                    if (!svg) return;

                    // Reset canvas & paskan ukuran canvas dengan area scroll
                    svg.innerHTML = '';
                    svg.style.width = wrapper.scrollWidth + 'px';
                    svg.style.height = wrapper.scrollHeight + 'px';

                    // Group kartu berdasarkan data-pair-id
                    const pairGroups = {};
                    wrapper.querySelectorAll('[data-member-card]').forEach(function(card) {
                        const pairId = card.dataset.pairId;
                        if (pairId && pairId !== '' && pairId !== '0') {
                            if (!pairGroups[pairId]) {
                                pairGroups[pairId] = [];
                            }
                            pairGroups[pairId].push(card);
                        }
                    });

                    // Helper mencari posisi relatif titik tengah bawah kartu
                    function getRelativePos(element, container) {
                        const elRect = element.getBoundingClientRect();
                        const cntRect = container.getBoundingClientRect();
                        return {
                            centerX: elRect.left - cntRect.left + container.scrollLeft + (elRect.width / 2),
                            bottomY: elRect.bottom - cntRect.top + container.scrollTop
                        };
                    }

                    // Gambar garis untuk tiap pasangan
                    Object.keys(pairGroups).forEach(function(pairId) {
                        const cards = pairGroups[pairId];
                        if (cards.length < 2) return;

                        const posA = getRelativePos(cards[0], wrapper);
                        const posB = getRelativePos(cards[1], wrapper);

                        const startX = Math.min(posA.centerX, posB.centerX);
                        const endX = Math.max(posA.centerX, posB.centerX);
                        const startY = (posA.centerX < posB.centerX) ? posA.bottomY : posB.bottomY;
                        const endY = (posA.centerX < posB.centerX) ? posB.bottomY : posA.bottomY;

                        // Offset garis melengkung/siku di bawah kartu (35px)
                        const lineOffsetY = Math.max(startY, endY) + 35;
                        const midX = (startX + endX) / 2;

                        // Path Siku: Turun -> Mendatar -> Naik
                        const d =
                            `M ${startX} ${startY} L ${startX} ${lineOffsetY} L ${endX} ${lineOffsetY} L ${endX} ${endY}`;

                        // 1. Path Garis Oranye
                        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', d);
                        path.setAttribute('stroke', '#f59e0b'); // amber-500
                        path.setAttribute('stroke-width', '2.5');
                        path.setAttribute('fill', 'none');
                        svg.appendChild(path);

                        // 2. Badge Nominal Rp 500.000 di Tengah Garis
                        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                        g.setAttribute('transform', `translate(${midX}, ${lineOffsetY})`);

                        // Background Kapsul Putih
                        const badgeBg = document.createElementNS('http://www.w3.org/2000/svg',
                            'rect');
                        badgeBg.setAttribute('x', '-60');
                        badgeBg.setAttribute('y', '-16');
                        badgeBg.setAttribute('width', '120');
                        badgeBg.setAttribute('height', '32');
                        badgeBg.setAttribute('rx', '16');
                        badgeBg.setAttribute('fill', '#ffffff');
                        badgeBg.setAttribute('stroke', '#f59e0b');
                        badgeBg.setAttribute('stroke-width', '2');
                        badgeBg.setAttribute('filter', 'drop-shadow(0px 2px 4px rgba(0,0,0,0.06))');
                        g.appendChild(badgeBg);

                        // Lingkaran "Rp"
                        const iconCircle = document.createElementNS('http://www.w3.org/2000/svg',
                            'circle');
                        iconCircle.setAttribute('cx', '-38');
                        iconCircle.setAttribute('cy', '0');
                        iconCircle.setAttribute('r', '10');
                        iconCircle.setAttribute('fill', '#f59e0b');
                        g.appendChild(iconCircle);

                        const iconText = document.createElementNS('http://www.w3.org/2000/svg',
                            'text');
                        iconText.setAttribute('x', '-38');
                        iconText.setAttribute('y', '3.5');
                        iconText.setAttribute('text-anchor', 'middle');
                        iconText.setAttribute('font-size', '8');
                        iconText.setAttribute('font-weight', 'bold');
                        iconText.setAttribute('fill', '#ffffff');
                        iconText.textContent = 'Rp';
                        g.appendChild(iconText);

                        // Teks "500.000"
                        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        text.setAttribute('x', '6');
                        text.setAttribute('y', '4');
                        text.setAttribute('text-anchor', 'middle');
                        text.setAttribute('font-size', '12');
                        text.setAttribute('font-weight', '800');
                        text.setAttribute('fill', '#334155');
                        text.textContent = '500.000';
                        g.appendChild(text);

                        svg.appendChild(g);
                    });
                });
            }

            // Jalankan fungsi saat DOM Siap & window resize
            drawPairLines();
            setTimeout(drawPairLines, 300);
            window.addEventListener('resize', drawPairLines);
        });
    </script>
@endsection
