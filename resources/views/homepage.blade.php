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
</head>

<body class="bg-[#faf8f1] text-slate-800 antialiased">

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
                <a href="#tentang" class="text-sm font-medium text-white/80 transition hover:text-gold-300">
                    Tentang
                </a>
                <a href="#sistem" class="text-sm font-medium text-white/80 transition hover:text-gold-300">
                    Sistem
                </a>
                <a href="#bonus" class="text-sm font-medium text-white/80 transition hover:text-gold-300">
                    Bonus
                </a>
                <a href="#reward" class="text-sm font-medium text-white/80 transition hover:text-gold-300">
                    Reward
                </a>
                <a href="#cara-bergabung" class="text-sm font-medium text-white/80 transition hover:text-gold-300">
                    Cara Bergabung
                </a>
            </div>

            <a href="{{ route('login') }}"
                class="rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 px-5 py-2.5 text-sm font-bold text-[#3b2b05] shadow-lg transition hover:scale-105 hover:from-gold-300 hover:to-gold-400">
                Login
            </a>
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
                        <a href="http://127.0.0.1:8080/paket-umroh-haji" target="_blank" rel="noopener noreferrer"
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

                            <div class="brosur-slide absolute inset-0 opacity-100 transition-opacity duration-1000">
                                <img src="{{ asset('images/slider/brosur1.jpeg') }}" alt="Brosur AGANDA 1"
                                    class="h-full w-full object-cover">
                            </div>

                            <div class="brosur-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
                                <img src="{{ asset('images/slider/brosur2.jpeg') }}" alt="Brosur AGANDA 2"
                                    class="h-full w-full object-cover">
                            </div>

                            <div class="brosur-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
                                <img src="{{ asset('images/slider/brosur3.jpeg') }}" alt="Brosur AGANDA 3"
                                    class="h-full w-full object-cover">
                            </div>

                            <div class="brosur-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
                                <img src="{{ asset('images/slider/brosur4.jpeg') }}" alt="Brosur AGANDA 4"
                                    class="h-full w-full object-cover">
                            </div>

                            <div class="brosur-slide absolute inset-0 opacity-0 transition-opacity duration-1000">
                                <img src="{{ asset('images/slider/brosur5.jpeg') }}" alt="Brosur AGANDA 5"
                                    class="h-full w-full object-cover">
                            </div>

                            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                                <span
                                    class="brosur-dot h-2 w-6 rounded-full bg-gold-400 transition-all duration-500"></span>
                                <span
                                    class="brosur-dot h-2 w-2 rounded-full bg-white/70 transition-all duration-500"></span>
                                <span
                                    class="brosur-dot h-2 w-2 rounded-full bg-white/70 transition-all duration-500"></span>
                                <span
                                    class="brosur-dot h-2 w-2 rounded-full bg-white/70 transition-all duration-500"></span>
                                <span
                                    class="brosur-dot h-2 w-2 rounded-full bg-white/70 transition-all duration-500"></span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        <section id="tentang" class="scroll-mt-24 bg-white py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="mx-auto max-w-3xl text-center">
                    <span class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">
                        Tentang AGANDA
                    </span>

                    <h2 class="mt-3 text-3xl font-black text-[#0b2f1f] sm:text-4xl">
                        Menjadi Agen, Membuka Peluang
                    </h2>

                    <p class="mt-5 leading-8 text-slate-600">
                        AGANDA merupakan sistem keagenan Agent Ganda Asia Andalas Wisata
                        yang menggabungkan pemasaran produk perjalanan dengan sistem bonus
                        dan reward untuk memberikan peluang berkembang bagi setiap agen.
                    </p>
                </div>

                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50/60 p-7">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#0b2f1f] text-2xl text-gold-400">
                            ✓
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-[#0b2f1f]">
                            Sistem Terstruktur
                        </h3>
                        <p class="mt-3 leading-7 text-slate-600">
                            Setiap agen memiliki struktur jaringan yang jelas sehingga
                            perkembangan jaringan dapat dipantau dengan lebih mudah.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-gold-100 bg-[#fffaf0] p-7">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gold-500 text-2xl text-white">
                            Rp
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-[#0b2f1f]">
                            Bonus Menarik
                        </h3>
                        <p class="mt-3 leading-7 text-slate-600">
                            Tersedia bonus sponsor dan bonus pasangan yang diberikan
                            berdasarkan aktivitas dan pencapaian jaringan.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50/60 p-7">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#0b2f1f] text-2xl text-gold-400">
                            ★
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-[#0b2f1f]">
                            Reward
                        </h3>
                        <p class="mt-3 leading-7 text-slate-600">
                            Pencapaian jaringan dapat memberikan kesempatan memperoleh
                            berbagai reward menarik sesuai target yang ditentukan.
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <section id="sistem" class="scroll-mt-24 bg-[#f4f7f1] py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="grid items-center gap-14 lg:grid-cols-2">

                    <div>
                        <span class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">
                            Sistem Matahari
                        </span>

                        <h2 class="mt-3 text-3xl font-black leading-tight text-[#0b2f1f] sm:text-4xl">
                            Sponsor Tanpa Batas,
                            <span class="text-gold-600">Peluang Berlipat</span>
                        </h2>

                        <p class="mt-5 leading-8 text-slate-600">
                            Dalam sistem AGANDA, seorang agen dapat mensponsori anggota
                            secara langsung tanpa batas. Setiap anggota yang berhasil
                            disponsori menjadi bagian dari jaringan agen.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex gap-4 rounded-2xl bg-white p-5 shadow-sm">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 font-black text-emerald-700">
                                    1
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#0b2f1f]">
                                        Sponsor Anggota
                                    </h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Agen mengajak dan mensponsori anggota baru ke dalam jaringan.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 rounded-2xl bg-white p-5 shadow-sm">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-100 font-black text-gold-700">
                                    2
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#0b2f1f]">
                                        Mendapat Bonus Sponsor
                                    </h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Setiap anggota yang berhasil disponsori memberikan bonus sponsor
                                        sesuai ketentuan AGANDA.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4 rounded-2xl bg-white p-5 shadow-sm">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 font-black text-emerald-700">
                                    3
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#0b2f1f]">
                                        Bangun Jaringan
                                    </h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        Jaringan dapat berkembang melalui sponsor langsung maupun
                                        perkembangan anggota di bawah jaringan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="rounded-[2rem] bg-[#0b2f1f] p-8 shadow-2xl sm:p-10">

                            <div class="text-center">
                                <div
                                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-4 border-gold-400 bg-white text-3xl font-black text-[#0b2f1f]">
                                    A
                                </div>

                                <div class="mx-auto mt-6 h-10 w-px bg-gold-400"></div>

                                <div class="mx-auto flex max-w-xs justify-between">
                                    <div class="h-px w-1/3 bg-gold-400"></div>
                                    <div class="h-px w-1/3 bg-gold-400"></div>
                                    <div class="h-px w-1/3 bg-gold-400"></div>
                                </div>

                                <div class="mt-0 grid grid-cols-3 gap-3">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-gold-400 bg-emerald-700 text-lg text-white">
                                            A
                                        </div>
                                        <span class="mt-2 text-xs text-emerald-100">Sponsor</span>
                                    </div>

                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-gold-400 bg-emerald-700 text-lg text-white">
                                            B
                                        </div>
                                        <span class="mt-2 text-xs text-emerald-100">Sponsor</span>
                                    </div>

                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-gold-400 bg-emerald-700 text-lg text-white">
                                            C
                                        </div>
                                        <span class="mt-2 text-xs text-emerald-100">Sponsor</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 rounded-2xl bg-white/10 p-6 text-center">
                                <div class="text-sm font-semibold text-emerald-100">
                                    BONUS SPONSOR
                                </div>
                                <div class="mt-2 text-4xl font-black text-gold-400">
                                    Rp3.000.000
                                </div>
                                <div class="mt-2 text-sm text-white/60">
                                    untuk setiap anggota yang berhasil disponsori
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="bonus" class="scroll-mt-24 bg-white py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="mx-auto max-w-3xl text-center">
                    <span class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">
                        Bonus AGANDA
                    </span>

                    <h2 class="mt-3 text-3xl font-black text-[#0b2f1f] sm:text-4xl">
                        Bonus Cash yang Menarik
                    </h2>

                    <p class="mt-5 leading-8 text-slate-600">
                        Selain bonus sponsor, AGANDA memiliki bonus pasangan yang
                        diperoleh ketika dua agen baru membentuk pasangan sesuai
                        struktur jaringan.
                    </p>
                </div>

                <div class="mt-14 grid gap-6 lg:grid-cols-2">

                    <div
                        class="group rounded-[2rem] border border-gold-200 bg-gradient-to-br from-[#fffdf5] to-[#fff7dc] p-8 shadow-gold transition hover:-translate-y-1">
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <span
                                    class="inline-flex rounded-full bg-gold-500 px-3 py-1 text-xs font-bold text-white">
                                    BONUS SPONSOR
                                </span>

                                <h3 class="mt-5 text-2xl font-black text-[#0b2f1f]">
                                    Bonus Cash Rp3 Juta
                                </h3>

                                <p class="mt-3 leading-7 text-slate-600">
                                    Setiap anggota yang berhasil disponsori memberikan
                                    kesempatan memperoleh bonus sponsor sesuai ketentuan sistem.
                                </p>
                            </div>

                            <div
                                class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-[#0b2f1f] text-xl font-black text-gold-400 sm:flex">
                                Rp
                            </div>
                        </div>

                        <div class="mt-8 rounded-2xl bg-[#0b2f1f] p-6 text-center">
                            <div class="text-4xl font-black text-gold-400">
                                Rp3.000.000
                            </div>
                            <div class="mt-2 text-sm text-white/60">
                                bonus sponsor
                            </div>
                        </div>
                    </div>

                    <div
                        class="group rounded-[2rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-8 shadow-lg transition hover:-translate-y-1">
                        <div class="flex items-start justify-between gap-5">
                            <div>
                                <span
                                    class="inline-flex rounded-full bg-emerald-700 px-3 py-1 text-xs font-bold text-white">
                                    BONUS PASANGAN
                                </span>

                                <h3 class="mt-5 text-2xl font-black text-[#0b2f1f]">
                                    Bonus Cash Rp500 Ribu
                                </h3>

                                <p class="mt-3 leading-7 text-slate-600">
                                    Ketika terdapat dua agen baru yang membentuk pasangan
                                    kiri dan kanan sesuai struktur, agen dapat memperoleh bonus pasangan.
                                </p>
                            </div>

                            <div
                                class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gold-500 text-xl font-black text-white sm:flex">
                                Rp
                            </div>
                        </div>

                        <div class="mt-8 rounded-2xl bg-emerald-700 p-6 text-center">
                            <div class="text-4xl font-black text-gold-300">
                                Rp500.000
                            </div>
                            <div class="mt-2 text-sm text-white/70">
                                bonus pasangan
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="reward" class="scroll-mt-24 bg-[#0b2f1f] py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="mx-auto max-w-3xl text-center">
                    <span class="text-sm font-bold uppercase tracking-[0.25em] text-gold-400">
                        Reward
                    </span>

                    <h2 class="mt-3 text-3xl font-black text-white sm:text-4xl">
                        Raih Target, Dapatkan Reward
                    </h2>

                    <p class="mt-5 leading-8 text-emerald-100/70">
                        Perkembangan jaringan hingga level tertentu memberikan
                        kesempatan memperoleh reward menarik.
                    </p>
                </div>

                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">

                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-center backdrop-blur">
                        <div class="text-4xl font-black text-gold-400">5</div>
                        <div class="mt-2 text-sm font-bold text-white">AGANDA</div>
                        <div class="mt-4 text-sm text-emerald-100/60">Handphone</div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-center backdrop-blur">
                        <div class="text-4xl font-black text-gold-400">10</div>
                        <div class="mt-2 text-sm font-bold text-white">AGANDA</div>
                        <div class="mt-4 text-sm text-emerald-100/60">Emas 5 Gram</div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-center backdrop-blur">
                        <div class="text-4xl font-black text-gold-400">20</div>
                        <div class="mt-2 text-sm font-bold text-white">AGANDA</div>
                        <div class="mt-4 text-sm text-emerald-100/60">Motor</div>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-center backdrop-blur">
                        <div class="text-4xl font-black text-gold-400">50</div>
                        <div class="mt-2 text-sm font-bold text-white">AGANDA</div>
                        <div class="mt-4 text-sm text-emerald-100/60">Umroh Gratis</div>
                    </div>

                    <div class="rounded-3xl border border-gold-400/30 bg-gold-400/10 p-6 text-center backdrop-blur">
                        <div class="text-4xl font-black text-gold-400">100</div>
                        <div class="mt-2 text-sm font-bold text-white">AGANDA</div>
                        <div class="mt-4 text-sm text-emerald-100/70">
                            Umroh + Wisata Turki
                        </div>
                    </div>

                </div>

                <div
                    class="mx-auto mt-10 max-w-3xl rounded-2xl border border-gold-400/20 bg-gold-400/10 px-6 py-5 text-center text-sm leading-6 text-emerald-100/80">
                    Reward diberikan berdasarkan pencapaian dan ketentuan program AGANDA yang berlaku.
                </div>

            </div>
        </section>

        <section id="cara-bergabung" class="scroll-mt-24 bg-[#f8f7f1] py-20 lg:py-28">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="mx-auto max-w-3xl text-center">
                    <span class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-600">
                        Cara Bergabung
                    </span>

                    <h2 class="mt-3 text-3xl font-black text-[#0b2f1f] sm:text-4xl">
                        Mulai Perjalanan Bersama AGANDA
                    </h2>
                </div>

                <div class="relative mt-14 grid gap-8 md:grid-cols-4">

                    <div class="relative rounded-3xl bg-white p-7 text-center shadow-sm">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#0b2f1f] text-xl font-black text-gold-400">
                            1
                        </div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Daftar
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Daftarkan diri sebagai anggota AGANDA.
                        </p>
                    </div>

                    <div class="relative rounded-3xl bg-white p-7 text-center shadow-sm">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gold-500 text-xl font-black text-white">
                            2
                        </div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Menjadi Agen
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Lengkapi proses dan status keagenan sesuai program.
                        </p>
                    </div>

                    <div class="relative rounded-3xl bg-white p-7 text-center shadow-sm">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#0b2f1f] text-xl font-black text-gold-400">
                            3
                        </div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Bangun Jaringan
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Sponsor anggota baru dan kembangkan jaringan AGANDA.
                        </p>
                    </div>

                    <div class="relative rounded-3xl bg-white p-7 text-center shadow-sm">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gold-500 text-xl font-black text-white">
                            4
                        </div>
                        <h3 class="mt-5 font-bold text-[#0b2f1f]">
                            Raih Bonus
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Nikmati bonus dan kejar target reward sesuai pencapaian.
                        </p>
                    </div>

                </div>
            </div>
        </section>

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
    </script>
</body>

</html>
