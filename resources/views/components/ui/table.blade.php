@props([
    'searchable' => true,
    'placeholder' => 'Cari…',
    'emptyTitle' => 'Belum ada data',
    'emptyMessage' => null,
])

{{--
    Tabel admin dengan pencarian client-side sederhana.
    Menggantikan DataTables + jQuery yang sebelumnya dimuat di semua halaman
    admin hanya untuk kebutuhan dasar ini.

    Pemakaian:
      <x-ui.table>
          <x-slot:head><th>…</th></x-slot:head>
          <tr data-row><td>…</td></tr>
      </x-ui.table>
--}}

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card"
    x-data="{
        query: '',
        get rows() { return Array.from($refs.body?.querySelectorAll('[data-row]') ?? []); },
        filter() {
            const q = this.query.trim().toLowerCase();
            let visible = 0;

            this.rows.forEach(row => {
                const match = !q || row.textContent.toLowerCase().includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            this.visible = visible;
        },
        visible: null,
    }"
    x-init="$nextTick(() => { visible = rows.length })">

    @if ($searchable || isset($actions))
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-4">
            @if ($searchable)
                <div class="relative min-w-[200px] flex-1 sm:max-w-xs">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                    <input type="search" x-model="query" @input="filter()" placeholder="{{ $placeholder }}"
                        class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-3 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                </div>
            @endif

            @isset($actions)
                <div class="flex flex-shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-gray-100 bg-gray-50/70 text-xs uppercase tracking-wide text-gray-500">
                <tr>{{ $head }}</tr>
            </thead>
            <tbody class="divide-y divide-gray-100" x-ref="body">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- Ditampilkan saat tabel kosong atau pencarian tidak menemukan apa pun. --}}
    <div x-show="visible === 0" x-cloak class="px-6 py-12 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-50">
            <i class="fa-solid fa-inbox text-xl text-primary-300"></i>
        </div>
        <p class="mt-3 font-semibold text-gray-900"
            x-text="query ? 'Tidak ada hasil untuk “' + query + '”' : @js($emptyTitle)"></p>
        @if ($emptyMessage)
            <p class="mt-1 text-sm text-gray-500" x-show="!query">{{ $emptyMessage }}</p>
        @endif
    </div>
</div>
