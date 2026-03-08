<?php

use Livewire\Volt\Component;
use App\Models\Kategori;
use App\Models\Tahapan;

new class extends Component {

    public Kategori $kategoris;
    public array $tahapans = [];

    public function mount(Kategori $kategori): void
    {
        $this->kategoris = $kategori;

        $this->tahapans = Tahapan::where('kategori_id', $kategori->id)
            ->orderBy('kode')
            ->get()
            ->map(fn($t) => [
                'kode' => $t->kode,
                'name' => $t->name,
            ])
            ->toArray();
    }
};
?>

<div>
    <x-header title="Detail {{ $kategoris->name }}" separator progress-indicator />

    <x-card class="p-7 rounded-lg shadow-md">
        {{-- Detail Barang --}}
        <p class="mb-3 font-semibold">Detail Tahapan</p>
        @forelse ($tahapans as $tahapan)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3 rounded-lg p-5 ">
            <div>
                <p class="mb-1 text-gray-500">Kode Tahapan</p>
                <p class="font-semibold">{{ $tahapan['kode'] ?? '-' }}</p>
            </div>
            <div>
                <p class="mb-1 text-gray-500">Nama Tahapan</p>
                <p class="font-semibold">{{ $tahapan['name'] ?? '-' }}</p>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-sm">Tidak ada detail tahapan untuk kategori ini.</p>
        @endforelse
    </x-card>

    <div class="flex justify-end mt-4">
        <x-button
            label="Kembali"
            link="/tahapans" />
    </div>
</div>