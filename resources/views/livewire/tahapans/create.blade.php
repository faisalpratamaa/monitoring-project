<?php

use Livewire\Volt\Component;
use App\Models\Kategori;
use App\Models\Tahapan;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new class extends Component {
    // We will use it later
    use Toast, WithFileUploads;

    #[Rule('required')]
    public ?int $kategori_id = null;

    public array $tahapans = [];

    public int $id = 0;

    public function save(): void
    {
        // Validate
        $this->validate();

        $this->validate([
            'tahapans' => 'required|array|min:1',
            'tahapans.*.name' => 'required',
        ]);

        foreach ($this->tahapans as $item) {
            $item['kode'] = 'THP' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
            Tahapan::create([
                'kategori_id' => $this->kategori_id,
                'name' => $item['name'],
                'kode' => $item['kode'],
            ]);
            $this->id++;
        }

        // You can toast and redirect to any route
        $this->success('Tahapan berhasil dibuat!', redirectTo: '/tahapans');
    }

    public function addDetail(): void
    {
        $this->tahapans[] = [
            'name' => null,
            'kode' => null,
        ];
    }

    public function removeDetail(int $index): void
    {
        unset($this->tahapans[$index]);
        $this->tahapans = array_values($this->tahapans);
    }

    public function with(): array
    {
        return [
            'kategori_options' => \App\Models\Kategori::all(),
        ];
    }

    public function mount(): void
    {
        $thp = Tahapan::all();
        $this->id = (int) substr($thp->last()->kode, 3);
        if (empty($this->tahapans)) {
            $this->tahapans[] = [
                'name' => null,
                'kode' => null,
            ];
            $this->id++;
        }
    }
};

?>

<div>
    <x-header title="Buat Tahapan" separator />

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
                        <x-select label="Kategori" wire:model.live="kategori_id" :options="$kategori_options" option-label="name" option-value="id" placeholder="Pilih Kategori" />
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Tahapan section  --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Detail Tahapan" subtitle="Pilih tahapan" size="text-2xl" />
                </div>

                <div class="col-span-4">
                    @foreach ($tahapans as $index => $item)
                    <div class="rounded-xl space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-1 gap-3">
                            <x-input label="Tahapan" wire:model.lazy="tahapans.{{ $index }}.name" />
                            <div class="flex justify-end">
                                <x-button spinner icon="o-trash" wire:click="removeDetail({{ $index }})"
                                    class="btn-error btn-sm" label="Hapus Item" />
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <x-button spinner icon="o-plus" label="Tambah Item" wire:click="addDetail"
                        class="btn-primary" />
                </div>
            </div>
        </x-card>

        <x-slot:actions>
            <x-button label="Cancel" link="/tahapans" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>