@extends('layouts.app')

@section('title', 'Struktur Member')

@section('page-title', 'Struktur Member')

@section('content')

    <div class="mx-auto max-w-7xl">

        <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('aganda.structures.admin') }}" class="transition hover:text-red-600">
                Struktur Group
            </a>
            <span>/</span>
            <span class="font-medium text-slate-700">
                {{ $selectedUser->name }}
            </span>
        </div>

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Struktur {{ $selectedUser->name }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Menampilkan 2 line jaringan di bawah member yang dipilih.
                </p>
            </div>

            <a href="{{ route('aganda.structures.admin') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Kembali ke Struktur
            </a>
        </div>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Member
                </p>
                <p class="mt-2 text-lg font-bold text-slate-900">
                    {{ $selectedUser->name }}
                </p>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $selectedUser->member_id }}
                </p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                    Total Komisi
                </p>
                <p class="mt-2 text-xl font-bold text-emerald-700">
                    Rp {{ number_format($tree['commission'] ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                    Total Reward
                </p>
                <p class="mt-2 text-xl font-bold text-amber-700">
                    Rp {{ number_format($tree['reward'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- CANVAS CONTAINER DENGAN PADDING BAWAH TAMBAHAN (pb-20) -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div id="structure-canvas" class="relative min-w-max px-8 pt-10 pb-20">

                <!-- Canvas SVG untuk menggambar garis pasangan -->
                <svg id="pair-lines" class="pointer-events-none absolute inset-0 z-10 overflow-visible"></svg>

                <div class="relative z-20 flex justify-center">
                    @include('aganda.structure.node', [
                        'node' => $tree,
                    ])
                </div>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('structure-canvas');
            const svg = document.getElementById('pair-lines');

            if (!canvas || !svg) return;

            function drawPairLines() {
                // Reset canvas & paskan ukurannya dengan area scrollable
                svg.innerHTML = '';
                svg.setAttribute('width', canvas.scrollWidth);
                svg.setAttribute('height', canvas.scrollHeight);

                const cards = Array.from(
                    canvas.querySelectorAll('[data-member-card][data-pair-id]')
                );

                const pairs = {};

                cards.forEach(function(card) {
                    const pairId = card.dataset.pairId;
                    if (!pairId || pairId === '' || pairId === '0') return;

                    if (!pairs[pairId]) {
                        pairs[pairId] = [];
                    }
                    pairs[pairId].push(card);
                });

                const canvasRect = canvas.getBoundingClientRect();

                Object.values(pairs).forEach(function(pairCards) {
                    if (pairCards.length !== 2) return;

                    const rect1 = pairCards[0].getBoundingClientRect();
                    const rect2 = pairCards[1].getBoundingClientRect();

                    // Koordinat tengah-bawah dari masing-masing kartu
                    const pos1 = {
                        x: rect1.left - canvasRect.left + canvas.scrollLeft + (rect1.width / 2),
                        y: rect1.bottom - canvasRect.top + canvas.scrollTop
                    };

                    const pos2 = {
                        x: rect2.left - canvasRect.left + canvas.scrollLeft + (rect2.width / 2),
                        y: rect2.bottom - canvasRect.top + canvas.scrollTop
                    };

                    const startX = Math.min(pos1.x, pos2.x);
                    const endX = Math.max(pos1.x, pos2.x);
                    const startY = (pos1.x <= pos2.x) ? pos1.y : pos2.y;
                    const endY = (pos1.x <= pos2.x) ? pos2.y : pos1.y;

                    // Offset 35px di bawah dasar kartu terendah
                    const lineOffsetY = Math.max(startY, endY) + 35;
                    const midX = (startX + endX) / 2;

                    // Path Garis Siku (Mendatar & Tegak Lurus)
                    const d =
                        `M ${startX} ${startY} L ${startX} ${lineOffsetY} L ${endX} ${lineOffsetY} L ${endX} ${endY}`;

                    // 1. Gambar Garis Oranye Solid
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    path.setAttribute('d', d);
                    path.setAttribute('fill', 'none');
                    path.setAttribute('stroke', '#f59e0b'); // Warna Oranye/Amber
                    path.setAttribute('stroke-width', '2.5');
                    path.setAttribute('stroke-linecap', 'round');
                    path.setAttribute('stroke-linejoin', 'round');
                    svg.appendChild(path);

                    // 2. Format Nominal Pasangan
                    const pairBonusRaw = pairCards[0].dataset.pairBonus || pairCards[1].dataset.pairBonus ||
                        '500000';
                    const bonusNum = parseInt(pairBonusRaw.toString().replace(/[^0-9]/g, '')) || 500000;
                    const formattedBonus = bonusNum.toLocaleString('id-ID');

                    // 3. Buat Badge Kapsul di Tengah Garis
                    const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                    g.setAttribute('transform', `translate(${midX}, ${lineOffsetY})`);

                    // Background Kapsul Putih dengan Border Oranye & Shadow
                    const bg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    bg.setAttribute('x', '-60');
                    bg.setAttribute('y', '-16');
                    bg.setAttribute('width', '120');
                    bg.setAttribute('height', '32');
                    bg.setAttribute('rx', '16');
                    bg.setAttribute('fill', '#ffffff');
                    bg.setAttribute('stroke', '#f59e0b');
                    bg.setAttribute('stroke-width', '2');
                    bg.setAttribute('filter', 'drop-shadow(0px 2px 4px rgba(0,0,0,0.08))');
                    g.appendChild(bg);

                    // Lingkaran Icon Oranye "Rp"
                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('cx', '-36');
                    circle.setAttribute('cy', '0');
                    circle.setAttribute('r', '10');
                    circle.setAttribute('fill', '#f59e0b');
                    g.appendChild(circle);

                    // Teks "Rp" Putih
                    const rpText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    rpText.setAttribute('x', '-36');
                    rpText.setAttribute('y', '3.5');
                    rpText.setAttribute('text-anchor', 'middle');
                    rpText.setAttribute('font-size', '8');
                    rpText.setAttribute('font-weight', 'bold');
                    rpText.setAttribute('fill', '#ffffff');
                    rpText.textContent = 'Rp';
                    g.appendChild(rpText);

                    // Teks Nominal Angka
                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', '8');
                    text.setAttribute('y', '4');
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('font-size', '12');
                    text.setAttribute('font-weight', '800');
                    text.setAttribute('fill', '#334155');
                    text.textContent = formattedBonus;
                    g.appendChild(text);

                    svg.appendChild(g);
                });
            }

            // Jalankan fungsi menggambar
            drawPairLines();
            setTimeout(drawPairLines, 300); // Antisipasi delay render layout
            window.addEventListener('resize', drawPairLines);
        });
    </script>

@endsection
