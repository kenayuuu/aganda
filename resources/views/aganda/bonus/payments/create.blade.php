@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <a href="{{ route('admin.bonus.payments.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-red-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6" />
                </svg>
                Kembali ke Pembayaran
            </a>

            <h1 class="mt-4 text-2xl font-bold text-slate-900">
                Tambah Pembayaran
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Catat pembayaran calon secara manual.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                <div class="text-sm font-bold text-red-700">
                    Terdapat kesalahan:
                </div>

                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.bonus.payments.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">
                    Data Pembayaran
                </h2>

                <div class="mt-6 grid gap-5 md:grid-cols-2">

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Calon
                        </label>

                        <select name="calon_id" id="calon_id" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                            <option value="">Pilih calon</option>

                            @foreach ($calons as $calon)
                                <option value="{{ $calon->id }}" data-price="{{ $calon->packageKegiatan->harga ?? 0 }}"
                                    data-deposit="{{ $calon->packageKegiatan->deposit ?? 0 }}" @selected(old('calon_id') == $calon->id)>
                                    {{ $calon->nama_lengkap }}
                                    — {{ $calon->packageKegiatan->nama_paket ?? 'Tanpa Paket' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Jenis Pembayaran
                        </label>

                        <select name="payment_type" id="payment_type" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                            <option value="">Pilih jenis</option>
                            <option value="dp" @selected(old('payment_type') === 'dp')>
                                DP
                            </option>
                            <option value="pelunasan" @selected(old('payment_type') === 'pelunasan')>
                                Pelunasan
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nominal Pembayaran
                        </label>

                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="1"
                            required placeholder="Contoh: 10000000"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">

                        <p class="mt-2 text-xs text-slate-400">
                            Masukkan nominal pembayaran sebenarnya.
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Tanggal Pembayaran
                        </label>

                        <input type="datetime-local" name="paid_at"
                            value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Catatan
                        </label>

                        <textarea name="notes" rows="4" placeholder="Catatan pembayaran jika diperlukan"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-red-400 focus:ring-2 focus:ring-red-100">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            <div id="package-info" class="hidden rounded-2xl border border-amber-200 bg-amber-50 p-6">
                <h2 class="text-sm font-bold text-amber-800">
                    Informasi Paket
                </h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">

                    <div>
                        <p class="text-xs text-amber-600">
                            Harga Paket
                        </p>

                        <p id="package-price" class="mt-1 text-lg font-bold text-amber-900">
                            Rp 0
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-amber-600">
                            Deposit
                        </p>

                        <p id="package-deposit" class="mt-1 text-lg font-bold text-amber-900">
                            Rp 0
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-amber-600">
                            Sisa Setelah DP
                        </p>

                        <p id="package-remaining" class="mt-1 text-lg font-bold text-amber-900">
                            Rp 0
                        </p>
                    </div>

                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.bonus.payments.index') }}"
                    class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                    class="rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-red-700">
                    Simpan Pembayaran
                </button>
            </div>
        </form>

    </div>

    <script>
        const calonSelect = document.getElementById('calon_id');
        const packageInfo = document.getElementById('package-info');
        const packagePrice = document.getElementById('package-price');
        const packageDeposit = document.getElementById('package-deposit');
        const packageRemaining = document.getElementById('package-remaining');

        function formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
        }

        function updatePackageInfo() {
            const option = calonSelect.options[calonSelect.selectedIndex];

            if (!option || !option.value) {
                packageInfo.classList.add('hidden');
                return;
            }

            const price = Number(option.dataset.price || 0);
            const deposit = Number(option.dataset.deposit || 0);
            const remaining = Math.max(0, price - deposit);

            packagePrice.textContent = formatRupiah(price);
            packageDeposit.textContent = formatRupiah(deposit);
            packageRemaining.textContent = formatRupiah(remaining);

            packageInfo.classList.remove('hidden');
        }

        calonSelect.addEventListener('change', updatePackageInfo);

        updatePackageInfo();
    </script>
@endsection
