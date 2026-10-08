<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calon;
use App\Models\PackageKegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalonController extends Controller
{
    private const BANK_OPTIONS = [
        'BCA',
        'BNI',
        'BRI',
        'Bank Mandiri',
        'BTN',
        'BSI',
        'CIMB Niaga',
        'Bank Danamon',
        'Bank Permata',
        'OCBC',
        'Maybank Indonesia',
        'Bank Jago',
        'SeaBank',
        'Lainnya',
    ];

    public function create(): View
    {
        $packages = PackageKegiatan::query()
            ->where('is_active', true)
            ->orderBy('tanggal_berlangsung')
            ->get();

        $banks = self::BANK_OPTIONS;

        return view('admin.calons.create', compact('packages', 'banks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'umur' => ['nullable', 'integer', 'min:0', 'max:120'],
            'alamat' => ['nullable', 'string'],
            'no_paspor' => ['nullable', 'string', 'max:255'],
            'no_kk' => ['nullable', 'string', 'max:255'],
            'no_ktp' => ['nullable', 'string', 'max:255'],
            'akta_kelahiran' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'jenis_perjalanan' => ['required', Rule::in(['wisata', 'umroh', 'haji'])],
            'tanggal_berangkat' => ['nullable', 'date'],
            'package_kegiatan_id' => [
                'required',
                Rule::exists('package_kegiatans', 'id')
                    ->where('is_active', true)
                    ->where('kategori', $request->input('jenis_perjalanan')),
            ],
            'nama_bank' => ['nullable', 'required_with:no_rekening', Rule::in(self::BANK_OPTIONS)],
            'nama_bank_lainnya' => [
                'exclude_unless:nama_bank,Lainnya',
                'required_if:nama_bank,Lainnya',
                'string',
                'max:100',
            ],
            'no_rekening' => ['nullable', 'required_with:nama_bank', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
        ], [
            'package_kegiatan_id.exists' => 'Pilih paket aktif yang sesuai dengan jenis perjalanan.',
            'nama_bank.required_with' => 'Pilih bank jika nomor rekening diisi.',
            'no_rekening.required_with' => 'Isi nomor rekening jika bank dipilih.',
            'nama_bank_lainnya.required_if' => 'Isi nama bank lainnya.',
        ]);

        if (($validated['nama_bank'] ?? null) === 'Lainnya') {
            $validated['nama_bank'] = $validated['nama_bank_lainnya'];
        }

        unset($validated['nama_bank_lainnya']);

        Calon::create($validated);

        return redirect()
            ->route('admin.calons.create')
            ->with('success', 'Data calon jemaah berhasil disimpan.');
    }
}
