<?php

use Livewire\Volt\Component;
use App\Models\Kategori;
use App\Models\Tahapan;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use App\Models\Progres;
use App\Models\Project;
use App\Models\User;
use App\Models\DetailProject;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    #[Rule('required')]
    public ?int $kategori_id = null;

    #[Rule('required')]
    public ?int $project_id = null;

    #[Rule('required')]
    public ?int $tahapan_id = null;

    #[Rule('required')]
    public ?string $tanggal = null;

    #[Rule('required')]
    public ?int $user_id = null;

    #[Rule('required')]
    public ?int $nilai = null;

    #[Rule('required')]
    public $file = null;
    
    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d\TH:i');
        $this->user_id = auth()->user()->id;
    }

    public function save(): void
    {
        // Validate
        $this->validate();

        $detailProject = DetailProject::where('tahapan_id', $this->tahapan_id)->where('project_id', $this->project_id)->first();

        if ($this->file) {
            $url = $this->file->store('progres_files', 'public');
            $data = "/storage/$url";
        }

        Progres::create([
            'detail_project_id' => $detailProject->id, // Assuming this is the correct relation
            'tanggal' => $this->tanggal,
            'user_id' => $this->user_id,
            'nilai' => $this->nilai,
            'file' => $data ?? null,
        ]);

        // You can toast and redirect to any route
        $this->success('Progres berhasil dibuat!', redirectTo: '/progress');
    }

    public function with(): array
    {
        return [
            'kategori_options' => \App\Models\Kategori::all(),
            'project_options' => \App\Models\Project::all(),
            'tahapan_options' => \App\Models\Tahapan::all(),
            'user_options' => \App\Models\User::all(),
        ];
    }
};

?>

<div>
    <x-header title="Buat Progres" separator />

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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <x-choices-offline label="Kategori" wire:model.live="kategori_id" :options="$kategori_options" placeholder="Pilih Kategori" single clearable searchable />
                        <x-choices-offline label="Project" wire:model.live="project_id" :options="$project_options" placeholder="Pilih Project" single clearable searchable />
                        <x-choices-offline label="Tahapan" wire:model.live="tahapan_id" :options="$tahapan_options" placeholder="Pilih Tahapan" single clearable searchable />
                        <x-input label="Tanggal" type="datetime-local" wire:model.live="tanggal" />
                        <x-input label="Nilai" type="number" wire:model.live="nilai" />
                        <x-file wire:model="file" label="File" hint="Only PDF" accept="application/pdf" />
                    </div>
                </div>
            </div>
        </x-card>

        <x-slot:actions>
            <x-button label="Cancel" link="/progress" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>