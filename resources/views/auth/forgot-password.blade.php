<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - AGANDA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50">

    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                <div
                    class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-red-600 shadow-lg shadow-red-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM5.5 20a6.5 6.5 0 0113 0M18 8.5v4m2-2h-4" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                    Lupa Password?
                </h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Jangan khawatir. Masukkan email akun kamu dan kami akan mengirimkan link untuk membuat password
                    baru.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">

                @if (session('status'))
                    <div
                        class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-red-500"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m0 3.75h.008v.008H12V16.5zM10.29 3.86l-8.18 14.14A2 2 0 003.84 21h16.32a2 2 0 001.73-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>

                            <div class="space-y-1 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">
                            Email
                        </label>

                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.92l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.614a2.25 2.25 0 01-1.07-1.92V6.75" />
                                </svg>
                            </div>

                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="Masukkan email kamu" autocomplete="email" required autofocus
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10">
                        </div>
                    </div>

                    <button type="submit"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-red-200 transition hover:bg-red-700 hover:shadow-red-300 focus:outline-none focus:ring-4 focus:ring-red-500/20 active:scale-[0.99]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.92l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.614a2.25 2.25 0 01-1.07-1.92V6.75" />
                        </svg>
                        Kirim Link Reset Password
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-100 pt-6 text-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali ke Login
                    </a>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                © {{ date('Y') }} AGANDA. All rights reserved.
            </p>

        </div>
    </div>

</body>

</html>
