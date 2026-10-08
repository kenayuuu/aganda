@extends('layouts.app')

@section('title', 'Reward AGANDA x ASIATUR')
@section('page-title', 'Reward')

@section('content')
    @php
        $rewardLevels = [
            [
                'level' => 1,
                'name' => 'Weekend & Outing',
                'target' => 2,
                'unit' => 'Teman',
                'description' => 'Weekend & Outing 2H1M di Santika Hotel atau resort pilihan.',
                'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Hotel resort dengan kolam renang',
                'highlight' => true,
            ],
            [
                'level' => 2,
                'name' => 'Tour Malaysia – Singapore',
                'target' => 10,
                'unit' => 'Pasang',
                'description' => 'Pilih perjalanan Malaysia – Singapore atau Malaysia – Thailand.',
                'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Pemandangan kota dan Marina Bay di Singapore',
                'highlight' => false,
            ],
            [
                'level' => 3,
                'name' => 'Tour China Muslim',
                'target' => 40,
                'unit' => 'Pasang',
                'description' => 'Perjalanan Tour China Muslim selama 7 hari.',
                'image' => 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Tembok Besar China di antara pegunungan',
                'highlight' => false,
            ],
            [
                'level' => 4,
                'name' => 'Tour Eropa',
                'target' => 100,
                'unit' => 'Pasang',
                'description' => 'Perjalanan wisata pilihan di Eropa selama 10 hari.',
                'image' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Jalan dan bangunan klasik di Paris',
                'highlight' => false,
            ],
            [
                'level' => 5,
                'name' => 'Mobil Listrik',
                'target' => 400,
                'unit' => 'Pasang',
                'description' => 'Mobil listrik dengan nilai hingga Rp150 juta.',
                'image' => 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Mobil listrik modern berwarna putih',
                'highlight' => false,
            ],
            [
                'level' => 6,
                'name' => 'Rumah Sederhana',
                'target' => 1000,
                'unit' => 'Pasang',
                'description' => 'Rumah sederhana dengan nilai Rp300 juta.',
                'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Rumah modern dengan halaman hijau',
                'highlight' => false,
            ],
            [
                'level' => 7,
                'name' => 'Dana Cash',
                'target' => 3000,
                'unit' => 'Pasang',
                'description' => 'Dana cash sebesar Rp1 miliar.',
                'image' => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?auto=format&fit=crop&w=1000&q=85',
                'image_alt' => 'Ilustrasi tabungan dan perencanaan keuangan',
                'highlight' => false,
            ],
        ];
    @endphp

    <div class="mx-auto max-w-[1500px] space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.977 2.89a1 1 0 00-.364 1.118l1.519 4.674c.3.921-.755 1.688-1.538 1.118l-3.977-2.89a1 1 0 00-1.176 0l-3.977 2.89c-.783.57-1.838-.197-1.538-1.118l1.519-4.674a1 1 0 00-.364-1.118L3.064 10.1c-.783-.57-.381-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.532-4.674z" />
                        </svg>
                    </span>

                    <span class="text-sm font-semibold text-slate-500">
                        AGANDA x ASIATUR
                    </span>
                </div>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                    Reward
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Daftar tingkatan reward dan pencapaian agen AGANDA
                </p>
            </div>

            {{-- STATUS --}}
            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-semibold text-emerald-700">
                    Program reward aktif
                </span>
            </div>

        </div>


        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- TOTAL --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Reward
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            7
                            <span class="text-sm font-medium text-slate-400">
                                Tingkatan
                            </span>
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 3c-2.64 0-5.064.85-7.046 2.291A11.955 11.955 0 003 12c0 2.64.85 5.064 2.291 7.046A11.955 11.955 0 0012 21c2.64 0 5.064-.85 7.046-2.291A11.955 11.955 0 0021 12c0-2.64-.85-5.064-2.291-7.046z" />
                        </svg>
                    </div>
                </div>

                <p class="mt-3 text-xs text-slate-400">
                    Program AGANDA x ASIATUR
                </p>
            </div>


            {{-- TERCAPAI --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Reward Tercapai
                        </p>

                        <p class="mt-2 text-2xl font-bold text-emerald-600">
                            0
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <p class="mt-3 text-xs text-slate-400">
                    Tingkatan yang telah diraih
                </p>
            </div>


            {{-- PROGRESS --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Progress Saat Ini
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            Level 1
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>

                <p class="mt-3 text-xs text-slate-400">
                    Mulai dari target pertama
                </p>
            </div>


            {{-- NEXT REWARD --}}
            <div class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-700">
                            Reward Berikutnya
                        </p>

                        <p class="mt-2 text-xl font-bold text-slate-900">
                            Ajak 2 Teman
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v8m-4-4h8m4 0a8 8 0 11-16 0 8 8 0 0116 0z" />
                        </svg>
                    </div>
                </div>

                <p class="mt-3 text-xs text-amber-700">
                    Weekend & Outing 2H1M
                </p>
            </div>

        </div>


        {{-- REWARD HEADER --}}
        <div class="rounded-xl border border-slate-200 bg-white">

            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Tingkatan Reward
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Progress reward berdasarkan target pencapaian.
                    </p>
                </div>

                <div class="rounded-lg bg-slate-50 px-3 py-2">
                    <span class="text-xs text-slate-500">
                        Progress saat ini
                    </span>
                    <span class="ml-1 text-xs font-bold text-slate-900">
                        0
                    </span>
                </div>

            </div>


            {{-- REWARD GRID --}}
            <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">

                @foreach ($rewardLevels as $reward)

                    <article
                        class="group overflow-hidden rounded-xl border
                        {{ $reward['highlight']
                            ? 'border-amber-300 ring-1 ring-amber-100'
                            : 'border-slate-200' }}
                        bg-white transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">

                        {{-- IMAGE --}}
                        <div class="relative h-40 overflow-hidden bg-slate-100">

                            <img
                                src="{{ $reward['image'] }}"
                                alt="{{ $reward['image_alt'] }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                onerror="this.style.display='none'">

                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                            {{-- LEVEL --}}
                            <div class="absolute left-3 top-3 flex items-center gap-2">

                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-sm font-bold text-slate-900 shadow">
                                    {{ $reward['level'] }}
                                </span>

                                <span class="rounded-md bg-slate-900/75 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white backdrop-blur">
                                    Level {{ $reward['level'] }}
                                </span>

                            </div>

                            {{-- HIGHLIGHT --}}
                            @if ($reward['highlight'])
                                <span class="absolute right-3 top-3 rounded-md bg-amber-400 px-2.5 py-1 text-[10px] font-bold text-amber-950 shadow-sm">
                                    TARGET PERTAMA
                                </span>
                            @endif

                            {{-- IMAGE TITLE --}}
                            <div class="absolute bottom-3 left-4 right-4">

                                <p class="text-[11px] font-medium uppercase tracking-wide text-white/70">
                                    Target {{ number_format($reward['target']) }}
                                    {{ $reward['unit'] }}
                                </p>

                                <h3 class="mt-0.5 text-lg font-bold text-white">
                                    {{ $reward['name'] }}
                                </h3>

                            </div>

                        </div>


                        {{-- CONTENT --}}
                        <div class="p-4">

                            <p class="min-h-[42px] text-sm leading-5 text-slate-600">
                                {{ $reward['description'] }}
                            </p>


                            {{-- PROGRESS --}}
                            <div class="mt-4">

                                <div class="mb-2 flex items-center justify-between">
                                    <span class="text-xs font-medium text-slate-500">
                                        Progress
                                    </span>

                                    <span class="text-xs font-semibold text-slate-700">
                                        0 / {{ number_format($reward['target']) }}
                                        {{ $reward['unit'] }}
                                    </span>
                                </div>

                                <div
                                    class="h-1.5 overflow-hidden rounded-full bg-slate-100"
                                    role="progressbar"
                                    aria-label="Progress Level {{ $reward['level'] }}"
                                    aria-valuemin="0"
                                    aria-valuemax="{{ $reward['target'] }}"
                                    aria-valuenow="0">

                                    <div
                                        class="h-full w-0 rounded-full
                                        {{ $reward['highlight'] ? 'bg-amber-500' : 'bg-slate-800' }}">
                                    </div>

                                </div>

                            </div>


                            {{-- FOOTER --}}
                            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">

                                <span class="flex items-center gap-1.5 text-xs font-medium text-slate-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                    Belum tercapai
                                </span>

                                <button
                                    type="button"
                                    class="reward-detail-button inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                                    data-level="{{ $reward['level'] }}"
                                    data-name="{{ $reward['name'] }}"
                                    data-target="{{ number_format($reward['target']) }} {{ $reward['unit'] }}"
                                    data-description="{{ $reward['description'] }}"
                                    data-image="{{ $reward['image'] }}"
                                    data-alt="{{ $reward['image_alt'] }}">

                                    Lihat Detail

                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>

                                </button>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

        </div>


        {{-- INFO --}}
        <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

            <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
            </svg>

            <p class="text-xs leading-5 text-slate-500">
                Progress saat ini ditampilkan sebagai 0 dan belum terhubung ke perhitungan pencapaian member.
                Pemberian hadiah mengikuti syarat dan ketentuan resmi program AGANDA x ASIATUR.
            </p>

        </div>

    </div>


    {{-- MODAL DETAIL --}}
    <div
        id="reward-detail-modal"
        class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="reward-modal-title">

        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

            <div class="relative h-52 bg-slate-100">

                <img
                    id="reward-modal-image"
                    src=""
                    alt=""
                    class="h-full w-full object-cover">

                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 to-transparent"></div>

                <button
                    id="reward-modal-close"
                    type="button"
                    aria-label="Tutup detail reward"
                    class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white text-xl text-slate-700 shadow transition hover:bg-slate-50">
                    &times;
                </button>

            </div>

            <div class="space-y-4 p-6">

                <div>
                    <p
                        id="reward-modal-level"
                        class="text-xs font-bold uppercase tracking-wider text-amber-600">
                    </p>

                    <h2
                        id="reward-modal-title"
                        class="mt-1 text-xl font-bold text-slate-900">
                    </h2>
                </div>

                <p
                    id="reward-modal-target"
                    class="inline-flex rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700">
                </p>

                <p
                    id="reward-modal-description"
                    class="text-sm leading-6 text-slate-600">
                </p>

                <div class="border-t border-slate-100 pt-4">

                    <div class="mb-2 flex justify-between text-xs">
                        <span class="font-medium text-slate-500">
                            Progress
                        </span>

                        <span class="font-semibold text-slate-700">
                            0
                        </span>
                    </div>

                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full w-0 rounded-full bg-amber-500"></div>
                    </div>

                </div>

                <p class="text-xs leading-5 text-slate-400">
                    Pencapaian dan pemberian reward tunduk pada ketentuan program yang berlaku.
                </p>

            </div>

        </div>

    </div>


    {{-- JAVASCRIPT --}}
    <script>
        const rewardDetailModal = document.getElementById('reward-detail-modal');
        const rewardModalImage = document.getElementById('reward-modal-image');

        document.querySelectorAll('.reward-detail-button').forEach((button) => {

            button.addEventListener('click', () => {

                document.getElementById('reward-modal-level').textContent =
                    `Level ${button.dataset.level}`;

                document.getElementById('reward-modal-title').textContent =
                    button.dataset.name;

                document.getElementById('reward-modal-target').textContent =
                    `Target ${button.dataset.target}`;

                document.getElementById('reward-modal-description').textContent =
                    button.dataset.description;

                rewardModalImage.src = button.dataset.image;
                rewardModalImage.alt = button.dataset.alt;

                rewardDetailModal.classList.remove('hidden');
                rewardDetailModal.classList.add('flex');

                document.body.classList.add('overflow-hidden');

                document.getElementById('reward-modal-close').focus();
            });

        });


        function closeRewardDetail() {

            rewardDetailModal.classList.add('hidden');
            rewardDetailModal.classList.remove('flex');

            document.body.classList.remove('overflow-hidden');

        }


        document.getElementById('reward-modal-close')
            .addEventListener('click', closeRewardDetail);


        rewardDetailModal.addEventListener('click', (event) => {

            if (event.target === rewardDetailModal) {
                closeRewardDetail();
            }

        });


        document.addEventListener('keydown', (event) => {

            if (
                event.key === 'Escape' &&
                !rewardDetailModal.classList.contains('hidden')
            ) {
                closeRewardDetail();
            }

        });
    </script>

@endsection
