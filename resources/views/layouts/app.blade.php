<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'AGANDA')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>


<body class="bg-slate-50 text-slate-800">

    <div class="min-h-screen">

        {{-- =====================================================
            SIDEBAR
        ====================================================== --}}

        <aside
            class="fixed inset-y-0 left-0 z-50 hidden w-64
                   border-r border-slate-200 bg-white
                   lg:flex lg:flex-col">

            {{-- =================================================
                LOGO
            ================================================== --}}

            <div class="flex h-20 shrink-0 items-center border-b border-slate-200 px-6">

                <div class="flex items-center gap-3">

                    <div>
                        <img src="{{ asset('images/aganda logo.jpeg') }}" alt="AGANDA Logo"
                            class="h-12 w-auto object-contain">
                    </div>

                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-slate-900 leading-none">
                            AGANDA
                        </h1>
                        <p class="text-xs text-slate-400 mt-1">
                            Management System
                        </p>
                    </div>

                </div>

            </div>


            {{-- =================================================
                NAVIGATION
            ================================================== --}}

            <nav class="flex-1 overflow-y-auto px-4 py-6">

                {{-- =================================================
                    MAIN
                ================================================== --}}

                <div class="mb-7">

                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Main
                    </p>


                    {{-- Dashboard --}}

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('admin.dashboard')
                                ? 'bg-red-50 text-red-600'
                                : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6" />
                        </svg>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </div>


                {{-- =================================================
                    GROUP
                ================================================== --}}

                <div class="mb-7">

                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Group
                    </p>


                    {{-- Semua Group --}}

                    <a href="{{ route('aganda.groups.index') }}"
                        class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                        {{ request()->routeIs('aganda.groups.index')
                            ? 'bg-red-50 text-red-600'
                            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>

                        <span>
                            Semua Group
                        </span>

                    </a>


                    {{-- Struktur Group --}}

                    <a href="{{ isset($group) ? route('aganda.groups.structure', $group->id) : route('aganda.groups.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
        {{ request()->routeIs('aganda.groups.structure')
            ? 'bg-red-50 text-red-600'
            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <span>
                            Struktur Group
                        </span>

                    </a>

                </div>


                {{-- =================================================
                    MEMBER
                ================================================== --}}

                <div class="mb-7">

                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Member
                    </p>


                    {{-- Semua Member --}}

                    <a href="#"
                        class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                               text-slate-500 hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                        </svg>

                        <span>
                            Semua Member
                        </span>

                    </a>


                    {{-- Downline --}}

                    <a href="#"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                               text-slate-500 hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M7 20l5-5 5 5M12 15V4M5 8l7-4 7 4" />
                        </svg>

                        <span>
                            Downline
                        </span>

                    </a>

                </div>


                {{-- =================================================
                    KEUANGAN
                ================================================== --}}

                <div class="mb-7">

                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Keuangan
                    </p>


                    {{-- Bonus --}}

                    <a href="#"
                        class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                               text-slate-500 hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4 1.12 4 2.5S14.21 18 12 18M12 5v14" />
                        </svg>

                        <span>
                            Bonus
                        </span>

                    </a>


                    {{-- Reward --}}

                    <a href="#"
                        class="mb-1 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                               text-slate-500 hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 3l2.7 5.46L21 9.38l-4.5 4.38 1.06 6.2L12 17.05 6.44 20l1.06-6.24L3 9.38l6.3-.92L12 3z" />
                        </svg>

                        <span>
                            Reward
                        </span>

                    </a>


                    {{-- Riwayat --}}

                    <a href="#"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                               text-slate-500 hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 5h6M9 3h6v2H9V3zM7 5H5v16h14V5h-2M9 10h6M9 14h6M9 18h4" />
                        </svg>

                        <span>
                            Riwayat
                        </span>

                    </a>

                </div>


                {{-- =================================================
                    SYSTEM
                ================================================== --}}

                <div>

                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        System
                    </p>


                    {{-- Pengaturan --}}

                    <a href="#"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                               text-slate-500 hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM19.4 15a1.7 1.7 0 000-3l1.1-.85-1.8-3.1-1.3.5a1.7 1.7 0 00-2.5-1.5L14.8 5h-3.6l-.1 1.5a1.7 1.7 0 00-2.5 1.5l-1.3-.5-1.8 3.1L6.6 11a1.7 1.7 0 000 3l-1.1.85 1.8 3.1 1.3-.5a1.7 1.7 0 002.5 1.5l.1 1.5h3.6l.1-1.5a1.7 1.7 0 002.5-1.5l1.3.5 1.8-3.1L19.4 15z" />
                        </svg>

                        <span>
                            Pengaturan
                        </span>

                    </a>

                </div>

            </nav>


            {{-- =====================================================
                SIDEBAR BOTTOM
            ====================================================== --}}

            <div class="shrink-0 border-t border-slate-200 p-4">

                <form action="{{ route('logout') }}" method="POST">

                    @csrf

                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-xl
                               px-4 py-3 text-sm font-medium
                               text-slate-500 transition
                               hover:bg-red-50 hover:text-red-600">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 12H3m0 0l4-4m-4 4l4 4M21 3v18" />
                        </svg>

                        <span>
                            Logout
                        </span>

                    </button>

                </form>

            </div>

        </aside>


        {{-- =====================================================
            MAIN CONTENT
        ====================================================== --}}

        <div class="lg:pl-64">

            {{-- =================================================
                TOPBAR
            ================================================== --}}

            <header
                class="sticky top-0 z-40 flex h-20 items-center
                       justify-between border-b border-slate-200
                       bg-white/90 px-6 backdrop-blur">

                {{-- Page Title --}}

                <div>

                    <h2 class="text-xl font-bold text-slate-900">
                        @yield('page-title', 'Dashboard')
                    </h2>

                    <p class="hidden text-sm text-slate-500 sm:block">
                        Management System AGANDA
                    </p>

                </div>


                {{-- Topbar Right --}}

                <div class="flex items-center gap-4">


                    {{-- =================================================
                        NOTIFICATION
                    ================================================== --}}

                    <button type="button"
                        class="relative flex h-10 w-10 items-center justify-center
                               rounded-xl border border-slate-200
                               text-slate-500 transition
                               hover:bg-slate-50 hover:text-slate-700">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0" />
                        </svg>


                        {{-- Notification Indicator --}}

                        <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500"></span>

                    </button>


                    {{-- =================================================
                        USER
                    ================================================== --}}

                    <div class="flex items-center gap-3">

                        <div class="hidden text-right sm:block">

                            <p class="text-sm font-semibold text-slate-900">
                                {{ auth()->user()->name ?? 'User' }}
                            </p>

                            <p class="text-xs capitalize text-slate-500">
                                {{ auth()->user()->role ?? '' }}
                            </p>

                        </div>


                        {{-- Avatar --}}

                        <div
                            class="flex h-10 w-10 items-center justify-center
                                   rounded-xl bg-red-100
                                   font-bold text-red-600">

                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}

                        </div>

                    </div>

                </div>

            </header>


            {{-- =================================================
                PAGE CONTENT
            ================================================== --}}

            <main class="p-6">

                @yield('content')

            </main>

        </div>

    </div>


    @stack('scripts')

</body>

</html>
