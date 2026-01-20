<?php

use Livewire\Volt\Component;
use App\Models\Project;
use App\Models\Tahapan;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    // Component parameter
    public Project $project;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
    public array $tahapan = [];

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        $this->validate([
            'tahapan' => 'required|array|min:1',
            'tahapan.*.name' => 'required',
            'tahapan.*.bobot' => 'required|numeric|min:1',
        ]);

        $jumlah = 0;

        foreach ($this->tahapan as $tahapan) {
            $jumlah += $tahapan['bobot'];
        }

        if ($jumlah > 100) {
            $this->error('Jumlah bobot tidak boleh lebih dari 100.');
            return;
        }

        // Create
        $project = Project::create($data);

        foreach ($this->tahapan as $tahapan) {
            Tahapan::create([
                'project_id' => $project->id,
                'name' => $tahapan['name'],
                'bobot' => $tahapan['bobot'],
            ]);
        }

        // You can toast and redirect to any route
        $this->success('Project berhasil dibuat!', redirectTo: '/projects');
    }

    public function addDetail(): void
    {
        $this->tahapan[] = [
            'name' => null,
            'bobot' => null,
        ];
    }

    public function removeDetail(int $index): void
    {
        unset($this->tahapan[$index]);
        $this->tahapan = array_values($this->tahapan);
    }
};

?>

<div>
    <x-header title="Create" separator />

    <x-form wire:submit="save">
        {{-- Basic section  --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Basic" subtitle="Basic info from user" size="text-2xl" />
                </div>

                <div class="col-span-4 grid gap-3">
                    <x-input label="Nama Project" wire:model="name" placeholder="Contoh: Budi Santoso" />
                </div>
            </div>
        </x-card>

        {{-- Detail section  --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Tahapan Project" subtitle="Pilih tahapan project" size="text-2xl" />
                </div>

                <div class="col-span-4 space-y-4">

                    @foreach ($tahapan as $index => $item)
                    <div class="rounded-xl space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <x-input label="Nama Tahapan" wire:model="tahapan.{{ $index }}.name"
                                placeholder="Contoh: Desain" />
                            <x-input label="Bobot" wire:model="tahapan.{{ $index }}.bobot"
                                placeholder="Contoh: 50" type="number" />
                        </div>

                        <div class="flex justify-end">
                            <x-button wire:click="removeDetail({{ $index }})" icon="o-trash" label="Hapus"
                                class="btn-error btn-sm" />
                        </div>
                    </div>
                    @endforeach

                    <x-button icon="o-plus" class="btn-primary" wire:click="addDetail" label="Tambah Tahapan" />
                </div>
            </div>
        </x-card>

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>