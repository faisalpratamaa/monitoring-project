<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\StokObatExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Kategori;
use App\Models\Tahapan;
use App\Models\Progres;
use App\Models\Project;
use App\Models\User;

new class extends Component {
    use Toast;
    use WithPagination;

    public $today;
    public function mount(): void
    {
        $this->today = \Carbon\Carbon::today();
    }

    public string $search = '';
    public bool $drawer = false;
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];
    public int $filter = 0;
    public int $kategori_id = 0;

    public bool $exportModal = false; // ✅ Modal export

    public $page = [['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100'], ['id' => 500, 'name' => '500']];

    public int $perPage = 25; // Default jumlah data per halaman
    public function clear(): void
    {
        $this->reset(['search', 'kategori_id', 'filter']);
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-top');
    }

    // public function export(): mixed
    // {
    //     if (!$this->startDate || !$this->endDate) {
    //         $this->error('Pilih tanggal terlebih dahulu.');
    //         return null; // ✅ Sekarang tetap return sesuatu
    //     }

    //     $this->exportModal = false;
    //     $this->success('Export dimulai...', position: 'toast-top');

    //     return Excel::download(new StokObatExport($this->startDate, $this->endDate), 'stok-obat.xlsx');
    // }

    public function delete($id): void
    {
        $tahapan = Tahapan::findOrFail($id);
        $tahapan->delete();
        $this->warning('Tahapan berhasil dihapus', position: 'toast-top');
    }

    public function headers(): array
    {
        return [['key' => 'kode', 'label' => 'Kode', 'class' => 'w-8'], ['key' => 'kategori.name', 'label' => 'Nama Kategori', 'class' => 'w-36'], ['key' => 'project.name', 'label' => 'Nama Project', 'class' => 'w-36'], ['key' => 'tahapans.name', 'label' => 'Nama Tahapan', 'class' => 'w-36'], ['key' => 'tanggal', 'label' => 'Tanggal', 'class' => 'w-36'], ['key' => 'user.name', 'label' => 'Nama User', 'class' => 'w-36'], ['key' => 'nilai', 'label' => 'Nilai', 'class' => 'w-36'], ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-1'],];
    }

    public function progress(): LengthAwarePaginator
    {
        return Progres::query()
            ->with(['tahapans.kategori', 'tahapans.project', 'tahapans', 'users'])
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        if ($this->filter >= 0 && $this->filter < 2) {
            $this->filter = 0;
            if (!empty($this->search)) {
                $this->filter++;
            }
        }
        return [
            'progress' => $this->progress(),
            'headers' => $this->headers(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
        ];
    }

    public function updated($property): void
    {
        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }
};

?>

<div class="p-4 space-y-6">
    <x-header title="Detail Progress" separator progress-indicator>
        <x-slot:actions>
            <div class="flex flex-row sm:flex-row gap-2">
                <x-button label="Create" link="/progress/create" responsive icon="o-plus" class="btn-primary" />
            </div>
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" />
        </div>
        <div class="md:col-span-6">
            <x-input placeholder="Cari..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>
        <div class="md:col-span-1">
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel"
                badge="{{ $this->filter }}" badge-classes="badge-primary" />
        </div>
    </div>

    <!-- TABLE -->
    <x-card class="overflow-x-auto">
        <x-table :headers="$headers" :rows="$progress" :sort-by="$sortBy" with-pagination
            link="progress/{id}/detail?kategori={name}">

            @scope('cell_actions', $progress)
            <div class="flex">
                <x-button icon="o-pencil"
                    link="/progress/{{ $progress->id }}/edit?kategori={{ $progress->tahapans->kategori->name }}"
                    class="btn-ghost btn-sm text-yellow-500" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button
        class="w-full sm:w-[90%] md:w-1/2 lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Cari Invoice..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>