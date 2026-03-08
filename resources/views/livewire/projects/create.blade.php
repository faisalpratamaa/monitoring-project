<?php

use Livewire\Volt\Component;
use App\Models\Project;
use App\Models\Tahapan;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use App\Models\DetailProject;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    // Component parameter
    public Project $project;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|unique:master_projects,kode')]
    public string $kode = '';

    #[Rule('required')]
    public int $kategori_id = 0;

    #[Rule('required')]
    public string $target = '';

    #[Rule('required')]
    public int $anggaran = 0;

    #[Rule('required')]
    public string $waktu = '';

    #[Rule('required')]
    public string $tipe = '';

    #[Rule('required')]
    public string $pic = '';

    #[Rule('required')]
    public string $no_hp = '';

    #[Rule('required|email')]
    public string $email = '';

    public array $tahapans = [];

    public array $waktu_options = [[
        'id' => 'TW 1',
        'name' => 'TW 1',
    ], [
        'id' => 'TW 2',
        'name' => 'TW 2',
    ], [
        'id' => 'TW 3',
        'name' => 'TW 3',
    ], [
        'id' => 'TW 4',
        'name' => 'TW 4',
    ]];

    public array $tipe_options = [[
        'id' => 'New',
        'name' => 'New',
    ], [
        'id' => 'Carry Over',
        'name' => 'Carry Over',
    ]];

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        $this->validate([
            'tahapans' => 'required|array|min:1',
            'tahapans.*.id' => 'required|exists:tahapans,id',
            'tahapans.*.tahapan' => 'required',
            'tahapans.*.bobot' => 'required',
            'tahapans.*.nilai' => 'required',
        ]);

        // Create the project
        $project = Project::create([
            'name' => $this->name,
            'kode' => $this->kode,
            'kategori_id' => $this->kategori_id,
            'bobot' => 0,
            'target' => $this->target,
            'anggaran' => $this->anggaran,
            'waktu' => $this->waktu,
            'tipe' => $this->tipe,
            'pic' => $this->pic,
            'no_hp' => $this->no_hp,
            'email' => $this->email,
        ]);

        foreach ($this->tahapans as $item) {
            DetailProject::create([
                'tahapan_id' => $item['id'],
                'project_id' => $project->id,
                'bobot' => $item['bobot'],
                'nilai' => $item['nilai'],
                'progres' => 0,
            ]);
        }

        // You can toast and redirect to any route
        $this->success('Project berhasil dibuat!', redirectTo: '/projects');
    }

    public function with(): array
    {
        return [
            'kategori_options' => \App\Models\Kategori::all(),
            'waktu_options' => $this->waktu_options,
            'tipe_options' => $this->tipe_options,
        ];
    }

    public function mount(): void
    {
        $tahun = now()->format('Y');
        $lastKode = Project::where('kode', 'like', $tahun . '%')
            ->orderBy('kode', 'desc')
            ->value('kode');
        if ($lastKode) {
            $lastNumber = (int) substr($lastKode, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $this->kode = $tahun . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function updatedKategoriId($value): void
    {
        $this->tahapans = [];
        $kategori = Tahapan::where('kategori_id', $value)->get();
        foreach ($kategori as $index => $item) {
            $this->tahapans[$index]['id'] = $item->id;
            $this->tahapans[$index]['tahapan'] = $item->name;
        }
    }
};

?>

<div>
    <x-header title="Buat Project" separator />

        @if ($errors->any())
        <div class="alert alert-error mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    <x-form wire:submit="save">
        {{-- Basic section  --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Basic" subtitle="Basic info from user" size="text-2xl" />
                </div>

                <div class="col-span-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <x-input label="Kode Project" wire:model="kode" readonly />
                        <x-input label="Nama Project" wire:model="name" placeholder="Contoh: Penyelesaian Aplikasi" />
                        <x-select label="Kategori" wire:model.live="kategori_id" :options="$kategori_options" option-label="name" option-value="id" placeholder="Pilih Kategori" />
                        <x-input label="Target (Bulan)" type="month" wire:model="target" />
                        <x-input label="Total Anggaran" wire:model="anggaran" prefix="Rp " money="IDR" />
                        <x-select label="Target (TW)" wire:model="waktu" :options="$waktu_options" option-label="name" option-value="id" placeholder="Pilih TW" />
                        <x-select label="Tipe" wire:model="tipe" :options="$tipe_options" option-label="name" option-value="id" placeholder="Pilih Tipe" />
                        <x-input label="PIC" wire:model="pic" placeholder="Contoh: Budi Santoso" />
                        <x-input label="No HP" wire:model.live="no_hp" type="tel" inputmode="numeric" maxlength="13" placeholder="08xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,13)" />
                        <x-input label="Email" wire:model="email" placeholder="Contoh: budi@example.com" />
                    </div>
                </div>
            </div>
        </x-card>

        @if(!empty($tahapans))
        {{-- Tahapan section  --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Tahapan" subtitle="Detail tahapan" size="text-2xl" />
                </div>

                <div class="col-span-4">
                    @foreach ($tahapans as $index => $item)
                    <div class="rounded-xl space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <x-input label="Tahapan" wire:model.lazy="tahapans.{{ $index }}.tahapan" readonly />
                            <x-input label="Bobot (%)" wire:model.lazy="tahapans.{{ $index }}.bobot" type="number" min="0" max="100" />
                            <x-input label="Nilai" wire:model.lazy="tahapans.{{ $index }}.nilai" type="number" min="0" />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </x-card>
        @endif

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>