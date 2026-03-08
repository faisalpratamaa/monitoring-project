<?php

use App\Models\Project;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\DetailProject;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public int $filter = 0;

    public $page = [
        ['id' => 10, 'name' => '10'],
        ['id' => 25, 'name' => '25'],
        ['id' => 50, 'name' => '50'],
        ['id' => 100, 'name' => '100'],
    ];

    public int $perPage = 10;

    /* =========================
     * ACTIONS
     * ========================= */
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-top');
    }

    public function delete($id): void
    {
        $detail_projects = DetailProject::where('project_id', $id)->get();
        foreach ($detail_projects as $detail_project) {
            $detail_project->delete();
        }
        $project = Project::findOrFail($id);
        $project->delete();

        $this->warning("Project {$project->name} berhasil dihapus", position: 'toast-top');
    }

    /* =========================
     * TABLE HEADERS
     * ========================= */
    public function headers(): array
    {
        return [
            ['key' => 'kode', 'label' => 'Kode Project', 'class' => 'w-24'],
            ['key' => 'name', 'label' => 'Nama Project', 'class' => 'w-64'],
            ['key' => 'kategori.name', 'label' => 'Kategori', 'class' => 'w-40'],
            ['key' => 'bobot', 'label' => 'Bobot', 'class' => 'w-20'],
            ['key' => 'target', 'label' => 'Target', 'class' => 'w-40'],
            ['key' => 'anggaran', 'label' => 'Anggaran', 'class' => 'w-32', 'format' => ['currency', 0, 'Rp']],
            ['key' => 'waktu', 'label' => 'Triwulan', 'class' => 'w-24'],
            ['key' => 'tipe', 'label' => 'Tipe', 'class' => 'w-32'],
            ['key' => 'pic', 'label' => 'PIC', 'class' => 'w-40'],
            ['key' => 'no_hp', 'label' => 'No. HP', 'class' => 'w-40'],
            ['key' => 'email', 'label' => 'Email', 'class' => 'w-48'],
            ['key' => 'actions', 'label' => 'Actions', 'class' => 'w-1'],
        ];
    }

    /* =========================
     * QUERY
     * ========================= */
    public function projects(): LengthAwarePaginator
    {
        return Project::query()
            ->with('kategori')
            ->when(
                $this->search,
                fn(Builder $q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage)
            ->through(function ($project) {
                $project->target = $this->formatTarget($project->target);
                return $project;
            });
    }

    /* =========================
     * FORMAT TARGET
     * ========================= */
    private function formatTarget(string $target): string
    {
        // Jika sudah format "YYYY NamaBulan" (contoh: 2026 Agustus)
        if (preg_match('/^\d{4}\s[A-Za-z]+$/', $target)) {
            return $target;
        }

        // Jika format "YYYY-MM"
        if (preg_match('/^\d{4}-\d{2}$/', $target)) {
            [$tahun, $bulan] = explode('-', $target);

            $bulanMap = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ];

            return $tahun . ' ' . ($bulanMap[$bulan] ?? '');
        }

        // Fallback (biar tidak error)
        return $target;
    }

    /* =========================
     * WITH
     * ========================= */
    public function with(): array
    {
        return [
            'projects' => $this->projects(),
            'headers' => $this->headers(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
        ];
    }

    /* =========================
     * RESET PAGINATION
     * ========================= */
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
    <x-header title="Daftar Project" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Create" link="/projects/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4  items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" />
        </div>
        <div class="md:col-span-6">
            <x-input placeholder="Name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                class="" />
        </div>
        <div class="md:col-span-1">
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel"
                badge="{{ $this->filter }}" badge-classes="badge-primary" />
        </div>
        <!-- Dropdown untuk jumlah data per halaman -->
    </div>

    <!-- TABLE wire:poll.5s="users"  -->
    <x-card>
        <x-table :headers="$headers" :rows="$projects" :sort-by="$sortBy" with-pagination>
            @scope('cell_actions', $project)
            <div class="flex">
                <x-button icon="o-pencil" link="projects/{{ $project['id'] }}/edit" spinner
                    class="btn-ghost btn-sm text-yellow-500" />
                <x-button icon="o-trash" wire:click="delete({{ $project['id'] }})"
                    wire:confirm="Yakin ingin menghapus {{ $project['name'] }}?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Name..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>