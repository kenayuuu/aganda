<section id="brosur" class="scroll-mt-24 bg-[#f8f7f1] py-20 lg:py-28">
    <style>
        .brochure-carousel {
            scrollbar-width: none;
        }

        .brochure-carousel::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <span class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">
                    Brosur Perjalanan
                </span>
                <h2 class="mt-3 text-3xl font-black text-[#0b2f1f] sm:text-4xl">
                    Pilih Perjalanan Anda
                </h2>
            </div>

            <p class="max-w-md text-sm leading-6 text-slate-500 sm:text-right">
                Lihat informasi paket perjalanan melalui koleksi brosur AGANDA. Klik brosur untuk melihat ukuran penuh.
            </p>
        </div>

        <p class="mt-8 text-center text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
            Geser ke kanan atau kiri untuk melihat brosur
        </p>

        <div class="mt-6 flex items-center gap-3 sm:mt-10">
            <button id="brochure-previous" type="button" aria-label="Geser brosur ke kiri"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#0b2f1f] text-xl text-gold-400 shadow-sm transition hover:bg-emerald-700">
                &larr;
            </button>

            <div id="brochure-carousel"
                class="brochure-carousel flex min-w-0 snap-x snap-mandatory gap-5 overflow-x-auto overscroll-x-contain pb-4">
            @foreach ($brochureImages as $index => $image)
                @php($number = $index + 1)

                <button type="button" class="brochure-card group min-w-[84%] snap-center text-left sm:min-w-[320px] lg:min-w-[360px]"
                    data-brochure-index="{{ $index }}"
                    aria-label="Lihat brosur {{ $number }}">
                    <div
                        class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white p-2 shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:border-gold-400 group-hover:shadow-xl">
                        <div class="aspect-[2/3] overflow-hidden rounded-2xl bg-slate-100">
                            <img src="{{ $image }}" alt="Brosur AGANDA {{ $number }}" loading="lazy"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2 pt-4">
                        <div>
                            <h3 class="font-bold text-[#0b2f1f]">Brosur Perjalanan {{ $number }}</h3>
                            <p class="mt-1 text-sm text-slate-500">Informasi paket ASIATUR</p>
                        </div>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0b2f1f] text-lg text-gold-400 transition group-hover:bg-gold-500 group-hover:text-white">
                            &rarr;
                        </span>
                    </div>
                </button>
            @endforeach
            </div>

            <button id="brochure-next" type="button" aria-label="Geser brosur ke kanan"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#0b2f1f] text-xl text-gold-400 shadow-sm transition hover:bg-emerald-700">
                &rarr;
            </button>
        </div>
    </div>
</section>

<div id="brochure-modal" class="fixed inset-0 z-50 hidden bg-[#071f15]/90 p-4 backdrop-blur-sm" role="dialog"
    aria-modal="true" aria-label="Pratinjau brosur">
    <div class="flex min-h-full items-center justify-center">
        <div class="relative flex max-h-[94vh] w-full max-w-4xl items-center justify-center">
            <button id="close-modal" type="button" aria-label="Tutup pratinjau"
                class="absolute right-2 top-2 z-10 flex h-11 w-11 items-center justify-center rounded-full bg-white text-2xl text-[#0b2f1f] shadow-lg transition hover:bg-gold-400">
                &times;
            </button>
            <img id="modal-image" src="" alt="Pratinjau brosur"
                class="max-h-[90vh] max-w-full rounded-2xl object-contain shadow-2xl">
            <button id="previous-brochure" type="button" aria-label="Brosur sebelumnya"
                class="absolute left-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-2xl text-[#0b2f1f] shadow-lg transition hover:bg-gold-400">
                &larr;
            </button>
            <button id="next-brochure" type="button" aria-label="Brosur berikutnya"
                class="absolute right-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-2xl text-[#0b2f1f] shadow-lg transition hover:bg-gold-400">
                &rarr;
            </button>
        </div>
    </div>
</div>

<script>
    const brochureImages = @json($brochureImages);
    const brochureModal = document.getElementById('brochure-modal');
    const modalImage = document.getElementById('modal-image');
    const brochureCarousel = document.getElementById('brochure-carousel');
    let currentBrochure = 0;

    function showBrochure(index) {
        currentBrochure = (index + brochureImages.length) % brochureImages.length;
        modalImage.src = brochureImages[currentBrochure];
        modalImage.alt = `Pratinjau brosur ${currentBrochure + 1}`;
        brochureModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeBrochure() {
        brochureModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('.brochure-card').forEach((card) => {
        card.addEventListener('click', () => showBrochure(Number(card.dataset.brochureIndex)));
    });

    document.getElementById('close-modal').addEventListener('click', closeBrochure);
    document.getElementById('previous-brochure').addEventListener('click', () => showBrochure(currentBrochure - 1));
    document.getElementById('next-brochure').addEventListener('click', () => showBrochure(currentBrochure + 1));
    document.getElementById('brochure-previous').addEventListener('click', () => {
        brochureCarousel.scrollBy({ left: -brochureCarousel.clientWidth * 0.85, behavior: 'smooth' });
    });
    document.getElementById('brochure-next').addEventListener('click', () => {
        brochureCarousel.scrollBy({ left: brochureCarousel.clientWidth * 0.85, behavior: 'smooth' });
    });

    brochureModal.addEventListener('click', (event) => {
        if (event.target === brochureModal) {
            closeBrochure();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (brochureModal.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            closeBrochure();
        }

        if (event.key === 'ArrowLeft') {
            showBrochure(currentBrochure - 1);
        }

        if (event.key === 'ArrowRight') {
            showBrochure(currentBrochure + 1);
        }
    });
</script>
