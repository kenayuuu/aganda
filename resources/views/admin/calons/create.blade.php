@extends('layouts.app')

@section('title', 'Input Calon Jemaah - AGANDA')

@section('page-title', 'Input Calon Jemaah')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm font-semibold text-red-600">Data Jemaah</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Input Calon Jemaah</h1>
            <p class="mt-2 text-sm text-slate-500">
                Masukkan identitas calon jemaah dan pilih paket perjalanan yang sesuai.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                Periksa kembali data yang dimasukkan. Beberapa kolom masih perlu diperbaiki.
            </div>
        @endif

        <form action="{{ route('admin.calons.store') }}" method="POST"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf

            <div class="border-b border-slate-100 px-5 py-4 sm:px-7">
                <h2 class="font-semibold text-slate-900">Informasi Calon</h2>
                <p class="mt-1 text-sm text-slate-500">Kolom bertanda * wajib diisi.</p>
            </div>

            <div class="grid gap-x-6 gap-y-5 px-5 py-6 sm:grid-cols-2 sm:px-7">
                <div class="sm:col-span-2">
                    <label for="nama_lengkap" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Nama lengkap <span class="text-red-600">*</span>
                    </label>
                    <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                        autocomplete="name" maxlength="255"
                        @class([
                            'w-full rounded-lg border px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100',
                            'border-red-400' => $errors->has('nama_lengkap'),
                            'border-slate-300' => !$errors->has('nama_lengkap'),
                        ])>
                    @error('nama_lengkap')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_telepon" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Nomor telepon <span class="text-red-600">*</span>
                    </label>
                    <input id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" required
                        autocomplete="tel" maxlength="255" inputmode="tel"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('no_telepon')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                        maxlength="255"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 border-t border-slate-100 pt-5">
                    <h3 class="text-sm font-semibold text-slate-900">Rekening jemaah <span class="font-normal text-slate-400">(opsional)</span></h3>
                    <p class="mt-1 text-xs text-slate-500">Jika nomor rekening diisi, bank wajib dipilih dan sebaliknya.</p>
                </div>

                <div>
                    <label for="nama_bank" class="mb-1.5 block text-sm font-medium text-slate-700">Bank</label>
                    <select id="nama_bank" name="nama_bank"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                        <option value="">Pilih bank</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank }}" @selected(old('nama_bank') === $bank)>{{ $bank }}</option>
                        @endforeach
                    </select>
                    @error('nama_bank')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_rekening" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor rekening</label>
                    <input id="no_rekening" name="no_rekening" value="{{ old('no_rekening') }}" maxlength="50"
                        inputmode="numeric" autocomplete="off"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('no_rekening')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="other-bank-field" class="sm:col-span-2" @if (old('nama_bank') !== 'Lainnya') hidden @endif>
                    <label for="nama_bank_lainnya" class="mb-1.5 block text-sm font-medium text-slate-700">Nama bank lainnya</label>
                    <input id="nama_bank_lainnya" name="nama_bank_lainnya" value="{{ old('nama_bank_lainnya') }}"
                        maxlength="100"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('nama_bank_lainnya')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="umur" class="mb-1.5 block text-sm font-medium text-slate-700">Umur</label>
                    <input id="umur" name="umur" type="number" min="0" max="120" value="{{ old('umur') }}"
                        inputmode="numeric"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('umur')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jenis_perjalanan" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Jenis perjalanan <span class="text-red-600">*</span>
                    </label>
                    <select id="jenis_perjalanan" name="jenis_perjalanan" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                        <option value="">Pilih jenis perjalanan</option>
                        @foreach (['wisata' => 'Wisata', 'umroh' => 'Umroh', 'haji' => 'Haji'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('jenis_perjalanan') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('jenis_perjalanan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="package_kegiatan_id" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Paket kegiatan <span class="text-red-600">*</span>
                    </label>
                    <select id="package_kegiatan_id" name="package_kegiatan_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                        <option value="">Pilih paket aktif</option>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}" data-category="{{ $package->kategori }}"
                                @selected((string) old('package_kegiatan_id') === (string) $package->id)>
                                {{ $package->nama_paket }} · {{ ucfirst($package->kategori) }}
                            </option>
                        @endforeach
                    </select>
                    @if ($packages->isEmpty())
                        <p class="mt-1 text-xs text-amber-700">Belum ada paket aktif. Aktifkan paket terlebih dahulu.</p>
                    @endif
                    @error('package_kegiatan_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_berangkat" class="mb-1.5 block text-sm font-medium text-slate-700">
                        Tanggal berangkat
                    </label>
                    <input id="tanggal_berangkat" name="tanggal_berangkat" type="date"
                        value="{{ old('tanggal_berangkat') }}"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('tanggal_berangkat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_paspor" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor paspor</label>
                    <input id="no_paspor" name="no_paspor" value="{{ old('no_paspor') }}" maxlength="255"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('no_paspor')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_ktp" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor KTP</label>
                    <input id="no_ktp" name="no_ktp" value="{{ old('no_ktp') }}" maxlength="255"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('no_ktp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_kk" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor KK</label>
                    <input id="no_kk" name="no_kk" value="{{ old('no_kk') }}" maxlength="255"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('no_kk')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="akta_kelahiran" class="mb-1.5 block text-sm font-medium text-slate-700">Nomor akta kelahiran</label>
                    <input id="akta_kelahiran" name="akta_kelahiran" value="{{ old('akta_kelahiran') }}" maxlength="255"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">
                    @error('akta_kelahiran')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="alamat" class="mb-1.5 block text-sm font-medium text-slate-700">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="catatan" class="mb-1.5 block text-sm font-medium text-slate-700">Catatan</label>
                    <textarea id="catatan" name="catatan" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
                <a href="{{ route('admin.members.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit" @disabled($packages->isEmpty())
                    class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                    Simpan Calon Jemaah
                </button>
            </div>
        </form>
    </div>

    <script>
        const journeyType = document.getElementById('jenis_perjalanan');
        const packageSelect = document.getElementById('package_kegiatan_id');
        const bankSelect = document.getElementById('nama_bank');
        const accountNumber = document.getElementById('no_rekening');
        const otherBankField = document.getElementById('other-bank-field');
        const otherBankName = document.getElementById('nama_bank_lainnya');

        journeyType.addEventListener('change', () => {
            [...packageSelect.options].forEach((option) => {
                option.hidden = option.value !== '' && option.dataset.category !== journeyType.value;
            });

            if (packageSelect.selectedOptions[0]?.dataset.category !== journeyType.value) {
                packageSelect.value = '';
            }
        });

        journeyType.dispatchEvent(new Event('change'));

        function updateBankFields() {
            const hasBank = bankSelect.value !== '';
            const hasAccountNumber = accountNumber.value.trim() !== '';
            const isOtherBank = bankSelect.value === 'Lainnya';

            bankSelect.required = hasAccountNumber;
            accountNumber.required = hasBank;
            otherBankField.hidden = !isOtherBank;
            otherBankName.required = isOtherBank;
        }

        bankSelect.addEventListener('change', updateBankFields);
        accountNumber.addEventListener('input', updateBankFields);
        updateBankFields();
    </script>
@endsection
