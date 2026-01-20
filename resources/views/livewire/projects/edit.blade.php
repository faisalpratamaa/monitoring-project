<?php

use Livewire\Volt\Component;
use App\Models\Project;
use App\Models\Tahapan;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast, WithFileUploads;

    public Project $project;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
    public array $tahapan = [];

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->name = $project->name;

        $this->tahapan = $project->tahapans->map(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'bobot' => $t->bobot,
        ])->toArray();
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required',
            'tahapan' => 'required|array|min:1',
            'tahapan.*.name' => 'required',
            'tahapan.*.bobot' => 'required|numeric|min:1',
        ]);

        $jumlah = collect($this->tahapan)->sum('bobot');

        if ($jumlah > 100) {
            $this->error('Jumlah bobot tidak boleh lebih dari 100.');
            return;
        }

        // Update project
        $this->project->update([
            'name' => $this->name,
        ]);

        // Ambil ID tahapan lama
        $existingIds = $this->project->tahapans()->pluck('id')->toArray();
        $currentIds = [];

        foreach ($this->tahapan as $item) {
            if (isset($item['id'])) {
                Tahapan::where('id', $item['id'])->update([
                    'name' => $item['name'],
                    'bobot' => $item['bobot'],
                ]);
                $currentIds[] = $item['id'];
            } else {
                $t = Tahapan::create([
                    'project_id' => $this->project->id,
                    'name' => $item['name'],
                    'bobot' => $item['bobot'],
                ]);
                $currentIds[] = $t->id;
            }
        }

        // Hapus tahapan yang dihapus di form
        $deletedIds = array_diff($existingIds, $currentIds);
        Tahapan::whereIn('id', $deletedIds)->delete();

        $this->success('Project berhasil diperbarui!', redirectTo: '/projects');
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
    <x-header title="Edit Project" separator />

    <x-form wire:submit="update">
        {{-- Basic section --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Basic" subtitle="Edit informasi project" size="text-2xl" />
                </div>

                <div class="col-span-4 grid gap-3">
                    <x-input label="Nama Project" wire:model="name" />
                </div>
            </div>
        </x-card>

        {{-- Tahapan --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Tahapan Project" subtitle="Edit tahapan project" size="text-2xl" />
                </div>

                <div class="col-span-4 space-y-4">
                    @foreach ($tahapan as $index => $item)
                    <div class="rounded-xl space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <x-input
                                label="Nama Tahapan"
                                wire:model="tahapan.{{ $index }}.name" />
                            <x-input
                                label="Bobot"
                                wire:model="tahapan.{{ $index }}.bobot"
                                type="number" />
                        </div>

                        <div class="flex justify-end">
                            <x-button
                                wire:click="removeDetail({{ $index }})"
                                icon="o-trash"
                                label="Hapus"
                                class="btn-error btn-sm" />
                        </div>
                    </div>
                    @endforeach

                    <x-button
                        icon="o-plus"
                        class="btn-primary"
                        wire:click="addDetail"
                        label="Tambah Tahapan" />
                </div>
            </div>
        </x-card>

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            <x-button
                label="Update"
                icon="o-check"
                spinner="update"
                type="submit"
                class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>