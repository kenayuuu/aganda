<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackageKegiatan;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = PackageKegiatan::query()
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_paket', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'packages' => $packages->map(function ($package) {
                return $this->formatPackage($package);
            })->values(),
        ]);
    }

    public function show(PackageKegiatan $package)
    {
        if (!$package->is_active) {
            return response()->json([
                'message' => 'Paket kegiatan tidak aktif.',
            ], 404);
        }

        return response()->json([
            'package' => $this->formatPackage($package),
        ]);
    }

    private function formatPackage(PackageKegiatan $package)
    {
        return [
            'id' => $package->id,
            'name' => $package->nama_paket
                ?? $package->name
                ?? null,
            'harga' => (float) $package->harga,
            'deposit' => (float) $package->deposit,
            'tanggal_berlangsung' => $package->tanggal_berlangsung,
            'is_active' => (bool) $package->is_active,
        ];
    }
}
