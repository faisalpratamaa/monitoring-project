<?php

use Livewire\Volt\Component;
use App\Models\Project;
use App\Models\Tahapan;
use App\Models\DetailProject;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast, WithFileUploads;

    // Parameter dari route
    public Project $project;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
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

    public array $waktu_options = [
        ['id' => 'TW 1', 'name' => 'TW 1'],
        ['id' => 'TW 2', 'name' => 'TW 2'],
        ['id' => 'TW 3', 'name' => 'TW 3'],
        ['id' => 'TW 4', 'name' => 'TW 4'],
    ];

    public array $tipe_options = [
        ['id' => 'New', 'name' => 'New'],
        ['id' => 'Carry Over', 'name' => 'Carry Over'],
    ];

    /**
     * MOUNT (LOAD DATA PROJECT)
     */
    public function mount(Project $project): void
    {
        $this->project = $project;

        $this->name = $project->name;
        $this->kode = $project->kode;
        $this->kategori_id = $project->kategori_id;
        $this->target = $project->target;
        $this->anggaran = $project->anggaran;
        $this->waktu = $project->waktu;
        $this->tipe = $project->tipe;
        $this->pic = $project->pic;
        $this->no_hp = $project->no_hp;
        $this->email = $project->email;

        // Load detail tahapan
        $this->tahapans = $project->detailProjects()
            ->with('tahapan')
            ->get()
            ->map(fn($item) => [
                'id' => $item->tahapan_id,
                'tahapan' => $item->tahapan->name,
                'bobot' => $item->bobot,
                'nilai' => $item->nilai,
            ])
            ->toArray();
    }

    /**
     * SAVE (UPDATE)
     */
    public function save(): void
    {
        $this->validate([
            'name' => 'required',
            'kode' => 'required|unique:master_projects,kode,' . $this->project->id,
            'kategori_id' => 'required',
            'target' => 'required',
            'anggaran' => 'required|numeric',
            'waktu' => 'required',
            'tipe' => 'required',
            'pic' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',

            'tahapans' => 'required|array|min:1',
            'tahapans.*.id' => 'required|exists:tahapans,id',
            'tahapans.*.bobot' => 'required|numeric',
            'tahapans.*.nilai' => 'required|numeric',
        ]);

        // Update project
        $this->project->update([
            'name' => $this->name,
            'kode' => $this->kode,
            'kategori_id' => $this->kategori_id,
            'target' => $this->target,
            'anggaran' => $this->anggaran,
            'waktu' => $this->waktu,
            'tipe' => $this->tipe,
            'pic' => $this->pic,
            'no_hp' => $this->no_hp,
            'email' => $this->email,
        ]);

        // Sync detail project
        foreach ($this->tahapans as $item) {
            DetailProject::updateOrCreate(
                [
                    'project_id' => $this->project->id,
                    'tahapan_id' => $item['id'],
                ],
                [
                    'bobot' => $item['bobot'],
                    'nilai' => $item['nilai'],
                ]
            );
        }

        $this->success('Project berhasil diperbarui!', redirectTo: '/projects');
    }

    /**
     * DATA UNTUK VIEW
     */
    public function with(): array
    {
        return [
            'kategori_options' => \App\Models\Kategori::all(),
            'waktu_options' => $this->waktu_options,
            'tipe_options' => $this->tipe_options,
        ];
    }

    /**
     * JIKA KATEGORI DIGANTI
     */
    public function updatedKategoriId($value): void
    {
        $this->tahapans = [];

        $kategori = Tahapan::where('kategori_id', $value)->get();

        foreach ($kategori as $index => $item) {
            $this->tahapans[$index]['id'] = $item->id;
            $this->tahapans[$index]['tahapan'] = $item->name;
            $this->tahapans[$index]['bobot'] = 0;
            $this->tahapans[$index]['nilai'] = 0;
        }
    }
};
?>

<div>
    <x-header title="Edit Project" separator />

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
        <x-card>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <x-input label="Kode Project" wire:model="kode" readonly />
                <x-input label="Nama Project" wire:model="name" />
                <x-select label="Kategori" wire:model.live="kategori_id"
                    :options="$kategori_options"
                    option-label="name"
                    option-value="id" />

                <x-input label="Target (Bulan)" type="month" wire:model="target" />
                <x-input label="Total Anggaran" wire:model="anggaran" prefix="Rp " money="IDR" />
                <x-select label="Target (TW)" wire:model="waktu"
                    :options="$waktu_options"
                    option-label="name"
                    option-value="id" />

                <x-select label="Tipe" wire:model="tipe"
                    :options="$tipe_options"
                    option-label="name"
                    option-value="id" />

                <x-input label="PIC" wire:model="pic" />
                <x-input label="No HP" wire:model="no_hp" />
                <x-input label="Email" wire:model="email" />
            </div>
        </x-card>

        @if(!empty($tahapans))
        <x-card>
            @foreach ($tahapans as $index => $item)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                <x-input label="Tahapan"
                    wire:model="tahapans.{{ $index }}.tahapan"
                    readonly />

                <x-input label="Bobot (%)"
                    type="number"
                    wire:model="tahapans.{{ $index }}.bobot" />

                <x-input label="Nilai"
                    type="number"
                    wire:model="tahapans.{{ $index }}.nilai" />
            </div>
            @endforeach
        </x-card>
        @endif

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            <x-button label="Update"
                icon="o-pencil"
                spinner="save"
                type="submit"
                class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>