@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">Profil Saya</h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola informasi profil dan keamanan akun kamu.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                <ul class="list-disc pl-5 text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('aganda.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <div class="text-center">

                        <div class="relative mx-auto w-32 h-32">
                            @if ($user->avatar)
                                <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                                    alt="Avatar">
                            @else
                                <div id="avatarPlaceholder"
                                    class="w-32 h-32 rounded-full bg-red-100 flex items-center justify-center border-4 border-white shadow-lg">
                                    <span class="text-4xl font-bold text-red-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>

                                <img id="avatarPreview"
                                    class="hidden w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
                                    alt="Preview Avatar">
                            @endif

                            <label for="avatar"
                                class="absolute bottom-1 right-1 w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center cursor-pointer hover:bg-red-700 transition shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </label>

                            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp"
                                class="hidden">
                        </div>

                        <h2 class="mt-4 text-lg font-semibold text-slate-800">
                            {{ $user->name }}
                        </h2>

                        <p class="text-sm text-slate-500">
                            {{ ucfirst($user->role) }}
                        </p>

                        <p class="mt-4 text-xs text-slate-400">
                            JPG, PNG atau WEBP
                            <br>
                            Maksimal 2 MB
                        </p>

                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6">

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-800">Informasi Pribadi</h2>
                            <p class="text-sm text-slate-500">
                                Perbarui informasi akun kamu.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                                Nama Lengkap
                            </label>

                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition"
                                required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                                Email
                            </label>

                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition"
                                required>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">
                                Nomor HP
                            </label>

                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Masukkan nomor HP">
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 mb-2">
                                Role
                            </label>

                            <input type="text" id="role" value="{{ ucfirst($user->role) }}"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500"
                                disabled>
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-slate-700 mb-2">
                                Alamat
                            </label>

                            <textarea id="address" name="address" rows="4"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Masukkan alamat">{{ old('address', $user->address) }}</textarea>
                        </div>

                    </div>

                    <div class="border-t border-slate-200 my-8"></div>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0h-2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 10V7a5 5 0 0110 0v3" />
                                <rect width="14" height="10" x="5" y="10" rx="2" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-800">Keamanan Akun</h2>
                            <p class="text-sm text-slate-500">
                                Kosongkan password jika tidak ingin mengubahnya.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                                Password Baru
                            </label>

                            <input type="password" id="password" name="password"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Masukkan password baru">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">
                                Konfirmasi Password
                            </label>

                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-100 outline-none transition"
                                placeholder="Ulangi password baru">
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-200">
                        <a href="{{ url()->previous() }}"
                            class="px-5 py-3 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </a>

                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-red-600 text-sm font-medium text-white hover:bg-red-700 transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');
        const avatarPlaceholder = document.getElementById('avatarPlaceholder');

        avatarInput.addEventListener('change', function() {
            const file = this.files[0];

            if (!file) {
                return;
            }

            avatarPreview.src = URL.createObjectURL(file);
            avatarPreview.classList.remove('hidden');

            if (avatarPlaceholder) {
                avatarPlaceholder.classList.add('hidden');
            }
        });
    </script>
@endsection
