<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGANDA - Agent Ganda Asia Andalas Wisata</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        aganda: {
                            50: '#f4faf6',
                            100: '#e5f4e9',
                            200: '#c8e7d0',
                            300: '#9dd0aa',
                            400: '#65b57a',
                            500: '#25834a',
                            600: '#176b3a',
                            700: '#12552f',
                            800: '#104329',
                            900: '#0b2f1f'
                        },
                        gold: {
                            300: '#f7df8a',
                            400: '#e8c34a',
                            500: '#d4a72c',
                            600: '#b88916',
                            700: '#8f6810'
                        }
                    },
                    boxShadow: {
                        gold: '0 10px 35px rgba(212, 167, 44, 0.18)'
                    }
                }
            }
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }

        .homepage-nav-link {
            position: relative;
            transition: color 220ms ease, transform 220ms ease;
        }

        .homepage-nav-link::after {
            position: absolute;
            right: 0;
            bottom: -8px;
            left: 0;
            height: 2px;
            border-radius: 999px;
            background: #e8c34a;
            content: '';
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 220ms ease;
        }

        .homepage-nav-link:hover,
        .homepage-nav-link.is-active {
            color: #f7df8a;
            transform: translateY(-1px);
        }

        .homepage-nav-link:hover::after,
        .homepage-nav-link.is-active::after {
            transform: scaleX(1);
        }

        .homepage-section-reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 700ms ease, transform 700ms ease;
        }

        .homepage-section-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .homepage-nav-link,
            .homepage-nav-link::after,
            .homepage-section-reveal {
                transition: none;
            }

            .homepage-section-reveal {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>

<body class="bg-[#faf8f1] text-slate-800 antialiased">
    @php
        $brochureImages = collect(glob(public_path('images/slider/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}'), GLOB_BRACE) ?: [])
            ->sort(fn (string $left, string $right): int => strnatcasecmp(basename($left), basename($right)))
            ->map(fn (string $path): string => asset('images/slider/' . basename($path)))
            ->values();
    @endphp

    <nav class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-[#0b2f1f]/95 shadow-lg backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ route('homepage') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/aganda logo.jpeg') }}" alt="AGANDA Logo"
                    class="h-12 w-auto rounded-xl object-contain">

                <div>
                    <div class="text-lg font-black tracking-wide text-white">
                        AGANDA
                    </div>
                    <div class="hidden text-[10px] font-medium tracking-wider text-emerald-200 sm:block">
                        AGENT GANDA ASIA ANDALAS WISATA
                    </div>
                </div>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="#tentang" data-nav-link class="homepage-nav-link text-sm font-medium text-white/80">
                    Tentang
                </a>
                <a href="#sistem" data-nav-link class="homepage-nav-link text-sm font-medium text-white/80">
                    Sistem
                </a>
                <a href="#bonus" data-nav-link class="homepage-nav-link text-sm font-medium text-white/80">
                    Bonus
                </a>
                <a href="#reward" data-nav-link class="homepage-nav-link text-sm font-medium text-white/80">
                    Reward
                </a>
                <a href="#cara-bergabung" data-nav-link class="homepage-nav-link text-sm font-medium text-white/80">
                    Cara Bergabung
                </a>
                <a href="#brosur" data-nav-link class="homepage-nav-link text-sm font-medium text-white/80">
                    Brosur
                </a>
            </div>

            <button id="mobile-menu-button" type="button"
                class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/20 text-white transition hover:border-gold-400 hover:text-gold-400 md:hidden"
                aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobile-menu">
                <span class="sr-only">Buka menu</span>
                <span class="flex w-5 flex-col gap-1.5">
                    <span class="mobile-menu-bar h-0.5 w-full rounded-full bg-current transition-transform"></span>
                    <span class="mobile-menu-bar h-0.5 w-full rounded-full bg-current transition-opacity"></span>
                    <span class="mobile-menu-bar h-0.5 w-full rounded-full bg-current transition-transform"></span>
                </span>
            </button>

            <a href="{{ route('login') }}"
                class="hidden rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 px-5 py-2.5 text-sm font-bold text-[#3b2b05] shadow-lg transition hover:scale-105 hover:from-gold-300 hover:to-gold-400 sm:inline-flex">
                Login
            </a>
        </div>

        <div id="mobile-menu" class="hidden border-t border-white/10 bg-[#0b2f1f] px-5 pb-5 pt-3 md:hidden">
            <div class="flex flex-col gap-1">
                <a href="#tentang" data-nav-link
                    class="homepage-nav-link rounded-lg px-3 py-3 text-sm font-medium text-white/80 hover:bg-white/10">
                    Tentang
                </a>
                <a href="#sistem" data-nav-link
                    class="homepage-nav-link rounded-lg px-3 py-3 text-sm font-medium text-white/80 hover:bg-white/10">
                    Sistem
                </a>
                <a href="#bonus" data-nav-link
                    class="homepage-nav-link rounded-lg px-3 py-3 text-sm font-medium text-white/80 hover:bg-white/10">
                    Bonus
                </a>
                <a href="#reward" data-nav-link
                    class="homepage-nav-link rounded-lg px-3 py-3 text-sm font-medium text-white/80 hover:bg-white/10">
                    Reward
                </a>
                <a href="#cara-bergabung" data-nav-link
                    class="homepage-nav-link rounded-lg px-3 py-3 text-sm font-medium text-white/80 hover:bg-white/10">
                    Cara Bergabung
                </a>
                <a href="#brosur" data-nav-link
                    class="homepage-nav-link rounded-lg px-3 py-3 text-sm font-medium text-white/80 hover:bg-white/10">
                    Brosur
                </a>
                <a href="{{ route('login') }}"
                    class="mt-2 rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 px-4 py-3 text-center text-sm font-bold text-[#3b2b05]">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <!-- SLIDER -->

    <div class="absolute bottom-5 left-1/2 z-10 flex -translate-x-1/2 gap-2">
        <button type="button" class="slider-dot h-2.5 w-2.5 rounded-full bg-white transition-all"
            data-slide="0"></button>

        <button type="button" class="slider-dot h-2.5 w-2.5 rounded-full bg-white/40 transition-all"
            data-slide="1"></button>

        <button type="button" class="slider-dot h-2.5 w-2.5 rounded-full bg-white/40 transition-all"
            data-slide="2"></button>
    </div>
    </section>

    <main>

        <section class="relative overflow-hidden bg-[#0b2f1f] pt-28">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(212,167,44,0.18),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(37,131,74,0.25),transparent_40%)]">
            </div>

            <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-5 py-20 lg:grid-cols-2 lg:px-8 lg:py-28">

                <div>
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-gold-400/30 bg-gold-400/10 px-4 py-2 text-sm font-semibold text-gold-300">
                        <span class="h-2 w-2 rounded-full bg-gold-400"></span>
                        SISTEM PEMASARAN AGANDA
                    </div>

                    <h1 class="max-w-3xl text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                        Berkahnya
                        <span class="text-gold-400">Berlipat Ganda</span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg leading-8 text-emerald-100/80">
                        Menjadi bagian dari AGANDA, Agent Ganda Asia Andalas Wisata,
                        dengan sistem pemasaran yang sederhana, peluang bonus yang menarik,
                        serta reward perjalanan dan hadiah berdasarkan pencapaian.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="#brosur"
                            class="rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 px-7 py-3.5 text-center font-bold text-[#3b2b05] shadow-gold transition hover:-translate-y-1">
                            Mulai Sekarang
                        </a>

                        <a href="#sistem"
                            class="rounded-xl border border-white/20 bg-white/5 px-7 py-3.5 text-center font-semibold text-white transition hover:bg-white/10">
                            Pelajari Sistem
                        </a>
                    </div>

                    <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
                        <div>
                            <div class="text-2xl font-black text-gold-400">Rp3 JT</div>
                            <div class="mt-1 text-xs text-emerald-100/60">Bonus Sponsor</div>
                        </div>

                        <div>
                            <div class="text-2xl font-black text-gold-400">Rp500K</div>
                            <div class="mt-1 text-xs text-emerald-100/60">Bonus Pasangan</div>
                        </div>

                        <div>
                            <div class="text-2xl font-black text-gold-400">5+</div>
                            <div class="mt-1 text-xs text-emerald-100/60">Target Reward</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-8 rounded-full bg-gold-400/10 blur-3xl"></div>

                    <div
                        class="relative mx-auto w-full max-w-[350px] overflow-hidden rounded-[2rem] border border-gold-400/20 bg-white/10 p-3 shadow-2xl backdrop-blur">

                        <div class="relative aspect-[2/3] overflow-hidden rounded-[1.5rem] bg-white">

                            @foreach ($brochureImages as $index => $image)
                                <div class="brosur-slide absolute inset-0 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-1000">
                                    <img src="{{ $image }}" alt="Brosur AGANDA {{ $index + 1 }}"
                                        class="h-full w-full object-cover">
                                </div>
                            @endforeach

                            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                                @foreach ($brochureImages as $index => $image)
                                    <span
                                        class="brosur-dot h-2 {{ $index === 0 ? 'w-6 bg-gold-400' : 'w-2 bg-white/70' }} rounded-full transition-all duration-500"></span>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        @include('homepage.tentang')
        @include('homepage.sistem')
        @include('homepage.bonus')
        @include('homepage.reward')
        @include('homepage.cara-bergabung')
        @include('homepage.brosur')
        <section class="bg-white py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">

                    <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="text-3xl text-gold-500">01</div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Modal Ringan
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Memulai peluang bisnis dengan konsep keagenan yang mudah dipahami.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="text-3xl text-gold-500">02</div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Bonus Ganda
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Terdapat bonus sponsor dan bonus pasangan berdasarkan sistem.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="text-3xl text-gold-500">03</div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Sistem Mudah
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Struktur jaringan dapat dipantau dengan lebih mudah melalui sistem AGANDA.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="text-3xl text-gold-500">04</div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Reward Nyata
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Tersedia berbagai reward berdasarkan pencapaian jaringan.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <section class="relative overflow-hidden bg-gradient-to-br from-[#0b2f1f] via-[#12552f] to-[#0b2f1f] py-20">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(212,167,44,0.16),transparent_40%)]">
            </div>

            <div class="relative mx-auto max-w-4xl px-5 text-center lg:px-8">
                <span class="text-sm font-bold uppercase tracking-[0.25em] text-gold-400">
                    Bergabung Bersama AGANDA
                </span>

                <h2 class="mt-4 text-3xl font-black text-white sm:text-5xl">
                    Saatnya Membangun Peluang
                    <span class="text-gold-400">Bersama</span>
                </h2>

                <p class="mx-auto mt-5 max-w-2xl leading-8 text-emerald-100/70">
                    Pelajari sistem AGANDA, bangun jaringan Anda, raih bonus,
                    dan kejar berbagai reward berdasarkan pencapaian.
                </p>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('login') }}"
                        class="rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 px-8 py-3.5 font-bold text-[#3b2b05] shadow-gold transition hover:-translate-y-1">
                        Login ke Sistem
                    </a>

                    <a href="#tentang"
                        class="rounded-xl border border-white/20 bg-white/5 px-8 py-3.5 font-semibold text-white transition hover:bg-white/10">
                        Pelajari Lagi
                    </a>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-[#071f15] text-white">
        <div class="mx-auto max-w-7xl px-5 py-12 lg:px-8">

            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/aganda logo.jpeg') }}" alt="AGANDA Logo"
                            class="h-12 w-auto rounded-xl object-contain">

                        <div>
                            <div class="text-xl font-black tracking-wide">
                                AGANDA
                            </div>
                            <div class="text-xs text-emerald-200/60">
                                Agent Ganda Asia Andalas Wisata
                            </div>
                        </div>
                    </div>

                    <p class="mt-5 max-w-lg text-sm leading-7 text-emerald-100/60">
                        Program terbaru dari ASIATUR TOURS & TRAVEL yang memberikan peluang bagi setiap agen untuk
                        mengembangkan jaringan, memperoleh bonus sponsor dan pasangan, serta meraih berbagai reward
                        menarik.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-gold-400">
                        Navigasi
                    </h3>

                    <div class="mt-4 space-y-3 text-sm text-emerald-100/60">
                        <a href="#tentang" class="block transition hover:text-white">
                            Tentang AGANDA
                        </a>
                        <a href="#sistem" class="block transition hover:text-white">
                            Sistem
                        </a>
                        <a href="#bonus" class="block transition hover:text-white">
                            Bonus
                        </a>
                        <a href="#reward" class="block transition hover:text-white">
                            Reward
                        </a>
                        <a href="#cara-bergabung" class="block transition hover:text-white">
                            Cara Bergabung
                        </a>
                        <a href="#brosur" class="block transition hover:text-white">
                            Brosur
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-gold-400">
                        Sistem AGANDA
                    </h3>

                    <div class="mt-4 space-y-3 text-sm text-emerald-100/60">
                        <div>Bonus Sponsor Rp3 Juta</div>
                        <div>Bonus Pasangan Rp500 Ribu</div>
                        <div>Akumulasi Jaringan</div>
                        <div>Reward Pencapaian</div>
                    </div>
                </div>

            </div>

            <div class="mt-10 border-t border-white/10 pt-6 text-center text-xs text-emerald-100/40">
                © {{ date('Y') }} AGANDA - Agent Ganda Asia Andalas Wisata. All rights reserved.
            </div>

        </div>
    </footer>

    <script>
        const brosurSlides = document.querySelectorAll('.brosur-slide');
        const brosurDots = document.querySelectorAll('.brosur-dot');

        let brosurIndex = 0;

        function showBrosur(index) {
            brosurSlides.forEach((slide, i) => {
                slide.classList.toggle('opacity-100', i === index);
                slide.classList.toggle('opacity-0', i !== index);
            });

            brosurDots.forEach((dot, i) => {
                dot.classList.toggle('bg-gold-400', i === index);
                dot.classList.toggle('bg-white/70', i !== index);
                dot.classList.toggle('w-6', i === index);
                dot.classList.toggle('w-2', i !== index);
            });

            brosurIndex = index;
        }

        setInterval(() => {
            brosurIndex = (brosurIndex + 1) % brosurSlides.length;
            showBrosur(brosurIndex);
        }, 5000);

        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuBars = mobileMenuButton.querySelectorAll('.mobile-menu-bar');

        const setMobileMenuState = (isOpen) => {
            mobileMenu.classList.toggle('hidden', !isOpen);
            mobileMenuButton.setAttribute('aria-expanded', String(isOpen));
            mobileMenuButton.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
            mobileMenuBars[0].classList.toggle('translate-y-2', isOpen);
            mobileMenuBars[0].classList.toggle('rotate-45', isOpen);
            mobileMenuBars[1].classList.toggle('opacity-0', isOpen);
            mobileMenuBars[2].classList.toggle('-translate-y-2', isOpen);
            mobileMenuBars[2].classList.toggle('-rotate-45', isOpen);
        };

        mobileMenuButton.addEventListener('click', () => {
            setMobileMenuState(mobileMenu.classList.contains('hidden'));
        });

        const navigationLinks = document.querySelectorAll('[data-nav-link]');
        const navigationSections = [...navigationLinks]
            .map((link) => document.querySelector(link.getAttribute('href')))
            .filter(Boolean);

        navigationSections.forEach((section) => {
            section.classList.add('homepage-section-reveal');
        });

        const setActiveNavigation = (activeId) => {
            navigationLinks.forEach((link) => {
                link.classList.toggle('is-active', link.getAttribute('href') === `#${activeId}`);
            });
        };

        navigationLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                const target = document.querySelector(link.getAttribute('href'));

                if (!target) {
                    return;
                }

                event.preventDefault();
                setActiveNavigation(target.id);
                setMobileMenuState(false);
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    setActiveNavigation(entry.target.id);
                }
            });
        }, {
            rootMargin: '-25% 0px -55% 0px',
            threshold: 0
        });

        navigationSections.forEach((section) => sectionObserver.observe(section));
    </script>
</body>

</html>
