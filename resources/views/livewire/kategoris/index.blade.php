<?php

use App\Models\Kategori;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];

    public int $filter = 0;

    public array $page = [
        ['id' => 10, 'name' => '10'],
        ['id' => 25, 'name' => '25'],
        ['id' => 50, 'name' => '50'],
        ['id' => 100, 'name' => '100'],
    ];

    public int $perPage = 10;

    /** MODAL */
    public bool $editModal = false;
    public bool $createModal = false;

    /** EDIT */
    public ?Kategori $editingKategori = null;
    public string $editingName = '';

    /** CREATE */
    public string $newKategoriName = '';

    /** CLEAR FILTER */
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-top');
    }

    /** DELETE */
    public function delete($id): void
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        $this->warning("Kategori {$kategori->name} dihapus.", position: 'toast-top');
    }

    /** OPEN CREATE MODAL */
    public function create(): void
    {
        $this->newKategoriName = '';
        $this->createModal = true;
    }

    /** SAVE CREATE */
    public function saveCreate(): void
    {
        $this->validate([
            'newKategoriName' => 'required|string|max:255',
        ]);

        Kategori::create([
            'name' => $this->newKategoriName,
        ]);

        $this->createModal = false;
        $this->success('Kategori berhasil dibuat.', position: 'toast-top');
    }

    /** OPEN EDIT MODAL */
    public function edit($id): void
    {
        $this->editingKategori = Kategori::find($id);

        if ($this->editingKategori) {
            $this->editingName = $this->editingKategori->name;
            $this->editModal = true;
        }
    }

    /** SAVE EDIT */
    public function saveEdit(): void
    {
        if ($this->editingKategori) {
            $this->validate([
                'editingName' => 'required|string|max:255',
            ]);

            $this->editingKategori->update([
                'name' => $this->editingName,
                'updated_at' => now(),
            ]);

            $this->editModal = false;
            $this->success('Kategori berhasil diperbarui.', position: 'toast-top');
        }
    }

    /** TABLE HEADERS */
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-12'],
            ['key' => 'name', 'label' => 'Nama Kategori', 'class' => 'w-64'],
            ['key' => 'projects_count', 'label' => 'Jumlah Project', 'class' => 'w-48'],
            ['key' => 'created_at', 'label' => 'Tanggal Dibuat', 'class' => 'w-48'],
            ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-1'],
        ];
    }

    /** DATA */
    public function kategoris(): LengthAwarePaginator
    {
        return Kategori::query()
            ->withCount('projects') // hitung jumlah project per kategori
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        return [
            'kategoris' => $this->kategoris(),
            'headers' => $this->headers(),
            'pages' => $this->page,
        ];
    }

    /** RESET PAGINATION */
    public function updated($property): void
    {
        if (!is_array($property) && $property !== '') {
            $this->resetPage();
        }
    }
};
?>

<div>
    <!-- HEADER -->
    <x-header title="Daftar Kategori" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Create" icon="o-plus" class="btn-primary" @click="$wire.create()" />
        </x-slot:actions>
    </x-header>

    <!-- FILTER -->
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" />
        </div>
        <div class="md:col-span-7">
            <x-input placeholder="Search..." wire:model.live.debounce="search"
                icon="o-magnifying-glass" clearable />
        </div>
    </div>

    <!-- TABLE -->
    <x-card>
        <x-table
            :headers="$headers"
            :rows="$kategoris"
            :sort-by="$sortBy"
            with-pagination
            @row-click="$wire.edit($event.detail.id)">
            @scope('cell_projects_count', $kategori)
            <span>{{ $kategori->projects_count }}</span>
            @endscope

            @scope('cell_actions', $kategori)
            <x-button
                icon="o-trash"
                wire:click="delete({{ $kategori['id'] }})"
                wire:confirm="Yakin ingin menghapus {{ $kategori['name'] }}?"
                spinner
                class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- CREATE MODAL -->
    <x-modal wire:model="createModal" title="Create Kategori">
        <div class="grid gap-4">
            <x-input label="Nama Kategori" wire:model.live="newKategoriName" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" icon="o-x-mark" @click="$wire.createModal=false" />
            <x-button label="Create" icon="o-check" class="btn-primary" wire:click="saveCreate" />
        </x-slot:actions>
    </x-modal>

    <!-- EDIT MODAL -->
    <x-modal wire:model="editModal" title="Edit Kategori">
        <div class="grid gap-4">
            <x-input label="Nama Kategori" wire:model.live="editingName" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" icon="o-x-mark" @click="$wire.editModal=false" />
            <x-button label="Save" icon="o-check" class="btn-primary" wire:click="saveEdit" />
        </x-slot:actions>
    </x-modal>
</div>