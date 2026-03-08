<?php

use Livewire\Volt\Component;
use App\Models\Kategori;
use App\Models\Tahapan;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast;

    #[Rule('required')]
    public ?int $kategori_id = null;

    public array $tahapans = [];

    public function mount(int $kategori): void
    {
        $this->kategori_id = $kategori;

        $this->tahapans = Tahapan::where('kategori_id', $kategori)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'kode' => $t->kode,
            ])
            ->toArray();

        if (empty($this->tahapans)) {
            $this->addDetail();
        }
    }

    public function update(): void
    {
        $this->validate([
            'kategori_id' => 'required',
            'tahapans' => 'required|array|min:1',
            'tahapans.*.name' => 'required',
        ]);

        $existingIds = Tahapan::where('kategori_id', $this->kategori_id)
            ->pluck('id')
            ->toArray();

        $currentIds = [];

        foreach ($this->tahapans as $item) {
            if (isset($item['id'])) {
                Tahapan::where('id', $item['id'])->update([
                    'name' => $item['name'],
                ]);
                $currentIds[] = $item['id'];
            } else {
                $t = Tahapan::create([
                    'kategori_id' => $this->kategori_id,
                    'name' => $item['name'],
                    'kode' => Tahapan::max('kode') + 1,
                ]);
                $currentIds[] = $t->id;
            }
        }

        // hapus yang dihapus dari form
        $deleted = array_diff($existingIds, $currentIds);
        Tahapan::whereIn('id', $deleted)->delete();

        $this->success('Tahapan berhasil diperbarui!', redirectTo: '/tahapans');
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
            'kategori_options' => Kategori::all(),
        ];
    }
};
?>

<div>
    <x-header title="Edit Tahapan" separator />

    @if ($errors->any())
    <div class="alert alert-error mb-4">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <x-form wire:submit="update">
        {{-- Basic section --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Basic" subtitle="Basic info from user" size="text-2xl" />
                </div>

                <div class="col-span-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <x-select
                            label="Kategori"
                            wire:model.live="kategori_id"
                            :options="$kategori_options"
                            option-label="name"
                            option-value="id"
                            disabled />
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Tahapan section --}}
        <x-card>
            <div class="lg:grid grid-cols-5">
                <div class="col-span-1">
                    <x-header title="Detail Tahapan" subtitle="Pilih tahapan" size="text-2xl" />
                </div>

                <div class="col-span-4">
                    @foreach ($tahapans as $index => $item)
                    <div class="rounded-xl space-y-3">
                        <div class="grid grid-cols-1 gap-3">
                            <x-input
                                label="Tahapan"
                                wire:model.lazy="tahapans.{{ $index }}.name" />

                            <div class="flex justify-end">
                                <x-button
                                    spinner
                                    icon="o-trash"
                                    wire:click="removeDetail({{ $index }})"
                                    class="btn-error btn-sm"
                                    label="Hapus Item" />
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <x-button
                        spinner
                        icon="o-plus"
                        label="Tambah Item"
                        wire:click="addDetail"
                        class="btn-primary" />
                </div>
            </div>
        </x-card>

        <x-slot:actions>
            <x-button label="Cancel" link="/tahapans" />
            <x-button
                label="Update"
                icon="o-check"
                spinner="update"
                type="submit"
                class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>