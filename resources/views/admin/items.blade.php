@extends('layouts.admin')

@section('title', 'Manajemen Peserta - Admin Harisma')
@section('page_title', 'Manajemen Peserta')
@section('page_subtitle', 'Kelola data peserta, tingkat kelas (Kelas X & XI), dan cabang lomba.')

@section('content')
<div x-data="{ 
    addModalOpen: false, 
    editModalOpen: false, 
    bulkModalOpen: false,
    editItemData: { id: null, category_id: '{{ $activeCategory?->id }}', title: '', subtitle: '', class_level: 'Kelas X', color: '#8C2D19', text_color: '#FFFFFF', weight: 1, is_active: true }
}">

    <!-- Top Action Bar & Filters -->
    <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md">
        
        <!-- Category & Class Level Tabs -->
        <div class="flex flex-wrap items-center gap-3">
            
            <!-- Category Tabs -->
            <div class="flex items-center space-x-1 bg-[#141622] p-1 rounded-lg border border-zinc-800">
                @foreach($categories as $cat)
                    <a href="{{ route('admin.items.index', ['category_id' => $cat->id, 'class_level' => request('class_level')]) }}" 
                       class="px-3 py-1.5 rounded text-xs font-semibold transition flex items-center space-x-2 {{ $activeCategory?->id == $cat->id ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
                        <i data-lucide="{{ $cat->icon }}" class="w-3.5 h-3.5"></i>
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Class Level Filter -->
            <div class="flex items-center space-x-1 bg-[#141622] p-1 rounded-lg border border-zinc-800">
                <a href="{{ route('admin.items.index', ['category_id' => $activeCategory?->id, 'class_level' => '']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-semibold transition {{ !request('class_level') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Semua Kelas
                </a>
                <a href="{{ route('admin.items.index', ['category_id' => $activeCategory?->id, 'class_level' => 'Kelas X']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-semibold transition {{ request('class_level') == 'Kelas X' ? 'bg-amber-500 text-zinc-950 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Kelas X
                </a>
                <a href="{{ route('admin.items.index', ['category_id' => $activeCategory?->id, 'class_level' => 'Kelas XI']) }}" 
                   class="px-3 py-1.5 rounded text-xs font-semibold transition {{ request('class_level') == 'Kelas XI' ? 'bg-amber-500 text-zinc-950 font-black' : 'text-zinc-400 hover:text-zinc-200' }}">
                    Kelas XI
                </a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <form action="{{ route('admin.items.clear-all') }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN INGIN MENGHAPUS SELURUH DATA PESERTA DARI SEMUA MATA LOMBA?\n\nTindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-lg bg-rose-950/80 hover:bg-rose-900 border border-rose-500/40 text-rose-300 hover:text-rose-200 font-bold text-xs transition-all shadow-md flex items-center space-x-1.5 transform hover:-translate-y-0.5 active:translate-y-0">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Hapus Semua Peserta</span>
                </button>
            </form>

            <button @click="bulkModalOpen = true" class="px-4 py-2 rounded-lg bg-[#161826] hover:bg-[#1D2033] text-amber-300 hover:text-amber-200 font-bold text-xs transition-all shadow-md border border-amber-500/30 flex items-center space-x-2 transform hover:-translate-y-0.5 active:translate-y-0">
                <i data-lucide="file-up" class="w-4 h-4"></i>
                <span>Bulk Import 18+ Nama</span>
            </button>

            <button @click="addModalOpen = true" class="px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-black text-xs transition-all shadow-lg shadow-amber-500/20 border border-amber-300/40 flex items-center space-x-2 transform hover:-translate-y-0.5 active:translate-y-0">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Peserta Baru</span>
            </button>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="p-5 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md space-y-4 mt-6">
        
        <!-- Table Header & Search -->
        <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-zinc-800">
            <div>
                <h3 class="text-sm font-bold text-amber-200 font-serif-ethnic">
                    Daftar Peserta: {{ $activeCategory?->name ?? 'Semua Kategori' }} {{ request('class_level') ? '(' . request('class_level') . ')' : '' }}
                </h3>
                <p class="text-xs text-zinc-400 mt-0.5">Total {{ $items->total() }} peserta terdaftar</p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.items.index') }}" class="flex items-center space-x-2">
                <input type="hidden" name="category_id" value="{{ $activeCategory?->id }}">
                <input type="hidden" name="class_level" value="{{ request('class_level') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peserta..." class="px-3 py-1.5 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500">
                <button type="submit" class="p-1.5 rounded bg-amber-500/20 text-amber-300 hover:bg-amber-500 hover:text-zinc-950 transition border border-amber-500/30">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-200">
                <thead class="bg-[#141622] text-zinc-400 uppercase text-[10px] font-bold border-b border-zinc-800">
                    <tr>
                        <th class="py-2.5 px-4">Warna Segmen</th>
                        <th class="py-2.5 px-4">Nama Peserta / Item</th>
                        <th class="py-2.5 px-4">Tingkat Kelas</th>
                        <th class="py-2.5 px-4">Detail / Subtitle</th>
                        <th class="py-2.5 px-4 text-center">Status Roda</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($items as $item)
                        <tr class="hover:bg-zinc-800/40 transition">
                            <!-- Color Swatch -->
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-2">
                                    <span class="w-4 h-4 rounded border border-white/20 shadow-sm flex-shrink-0" style="background-color: {{ $item->color }};"></span>
                                    <span class="font-mono text-[10px] text-zinc-400">{{ $item->color }}</span>
                                </div>
                            </td>

                            <!-- Title -->
                            <td class="py-3 px-4 font-bold text-zinc-100">
                                {{ $item->title }}
                            </td>

                            <!-- Class Level -->
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $item->class_level === 'Kelas XI' ? 'bg-purple-950 text-purple-300 border border-purple-500/30' : 'bg-amber-950 text-amber-300 border border-amber-500/30' }}">
                                    {{ $item->class_level ?? 'Kelas X' }}
                                </span>
                            </td>

                            <!-- Subtitle -->
                            <td class="py-3 px-4 text-zinc-400 italic">
                                {{ $item->subtitle ?? '-' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $item->is_active ? 'bg-emerald-950/80 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/80 border-rose-500/40 text-rose-300' }}">
                                    {{ $item->is_active ? '● AKTIF' : '○ NON-AKTIF' }}
                                </span>
                            </td>

                            <!-- 3-Dots Action Dropdown Menu -->
                            <td class="py-3 px-4 text-right">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" @click.outside="open = false" 
                                            class="p-1.5 rounded-lg bg-[#141622] hover:bg-zinc-800 text-zinc-300 hover:text-amber-300 transition border border-zinc-700/80 shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500/40">
                                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Dropdown Menu Box -->
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         x-cloak
                                         class="absolute right-0 z-30 mt-1 w-44 rounded-xl bg-[#121422] border border-zinc-700/80 shadow-2xl p-1.5 space-y-1 text-left">
                                        
                                        <button @click="
                                            editItemData = { 
                                                id: {{ $item->id }}, 
                                                category_id: '{{ $item->category_id }}', 
                                                title: '{{ addslashes($item->title) }}', 
                                                subtitle: '{{ addslashes($item->subtitle ?? '') }}', 
                                                class_level: '{{ $item->class_level ?? 'Kelas X' }}', 
                                                color: '{{ $item->color }}', 
                                                text_color: '{{ $item->text_color }}', 
                                                weight: {{ $item->weight }}, 
                                                is_active: {{ $item->is_active ? 'true' : 'false' }} 
                                            }; 
                                            editModalOpen = true;
                                            open = false;" 
                                            class="w-full flex items-center space-x-2 px-3 py-1.5 text-xs font-semibold text-amber-300 hover:bg-amber-500/10 rounded-lg transition">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                            <span>Edit Peserta</span>
                                        </button>

                                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full flex items-center space-x-2 px-3 py-1.5 text-xs font-semibold text-rose-400 hover:bg-rose-950/60 rounded-lg transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                <span>Hapus Peserta</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500 italic">
                                Belum ada data peserta terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-3 border-t border-zinc-800">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- MODAL 1: Tambah Peserta Baru -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="relative w-full max-w-lg bg-[#0E1017] rounded-xl border border-zinc-800 p-6 text-zinc-100 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Tambah Peserta Baru</h3>
                <button @click="addModalOpen = false" class="text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('admin.items.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Mata Lomba</label>
                        <select name="category_id" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            <option value="all" class="font-bold text-amber-300 bg-amber-950">★ SEMUA MATA LOMBA (Sekaligus)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $activeCategory?->id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Tingkat Kelas *</label>
                        <select name="class_level" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            <option value="Kelas X">Kelas X</option>
                            <option value="Kelas XI">Kelas XI</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-2 p-2 rounded bg-amber-500/10 border border-amber-500/20">
                    <input type="checkbox" name="apply_all_categories" id="apply_all_add" value="1" class="w-4 h-4 accent-amber-500">
                    <label for="apply_all_add" class="text-xs text-amber-300 font-bold">Otomatis tambahkan peserta ini ke SEMUA Mata Lomba</label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Nama Peserta / Item *</label>
                    <input type="text" name="title" required placeholder="Misal: Anita Rahayu (01)" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Subtitle / Detail (Opsional)</label>
                    <input type="text" name="subtitle" placeholder="Misal: Kelas X - Solo Vocal" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Warna Segmen</label>
                        <input type="color" name="color" value="#8C2D19" class="w-full h-9 rounded bg-[#141622] border border-zinc-800 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Bobot Probabilitas</label>
                        <input type="number" name="weight" value="1" min="1" max="100" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active_add" checked value="1" class="w-4 h-4 accent-amber-500">
                    <label for="is_active_add" class="text-xs text-zinc-300">Aktifkan peserta di roda spinwheel</label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-zinc-800">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 rounded bg-[#141622] text-xs font-semibold text-zinc-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs shadow-md">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Peserta -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="relative w-full max-w-lg bg-[#0E1017] rounded-xl border border-zinc-800 p-6 text-zinc-100 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Edit Data Peserta</h3>
                <button @click="editModalOpen = false" class="text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form :action="'/admin/items/' + editItemData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Mata Lomba</label>
                        <select name="category_id" x-model="editItemData.category_id" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Tingkat Kelas</label>
                        <select name="class_level" x-model="editItemData.class_level" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            <option value="Kelas X">Kelas X</option>
                            <option value="Kelas XI">Kelas XI</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Nama Peserta / Item *</label>
                    <input type="text" name="title" x-model="editItemData.title" required class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Subtitle / Detail</label>
                    <input type="text" name="subtitle" x-model="editItemData.subtitle" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Warna Segmen</label>
                        <input type="color" name="color" x-model="editItemData.color" class="w-full h-9 rounded bg-[#141622] border border-zinc-800 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Bobot Probabilitas</label>
                        <input type="number" name="weight" x-model="editItemData.weight" min="1" max="100" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="is_active" id="is_active_edit" :checked="editItemData.is_active" value="1" class="w-4 h-4 accent-amber-500">
                    <label for="is_active_edit" class="text-xs text-zinc-300">Aktifkan peserta di roda spinwheel</label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-zinc-800">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded bg-[#141622] text-xs font-semibold text-zinc-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Bulk Import 18+ Nama -->
    <div x-show="bulkModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="relative w-full max-w-lg bg-[#0E1017] rounded-xl border border-zinc-800 p-6 text-zinc-100 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Bulk Import 18+ Nama Peserta</h3>
                <button @click="bulkModalOpen = false" class="text-zinc-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('admin.items.bulk-import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Target Mata Lomba</label>
                        <select name="category_id" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            <option value="all" class="font-bold text-amber-300 bg-amber-950">★ SEMUA MATA LOMBA (Sekaligus)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $activeCategory?->id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Target Kelas</label>
                        <select name="class_level" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                            <option value="Kelas X">Kelas X</option>
                            <option value="Kelas XI">Kelas XI</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-2 p-2 rounded bg-amber-500/10 border border-amber-500/20">
                    <input type="checkbox" name="apply_all_categories" id="apply_all_bulk" value="1" checked class="w-4 h-4 accent-amber-500">
                    <label for="apply_all_bulk" class="text-xs text-amber-300 font-bold">Otomatis import ke SEMUA Mata Lomba (Solo Vocal, Tari Modern, Sketching, dll)</label>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Paste Teks Multi-Baris (Daftar 18+ Nama Peserta)</label>
                    <textarea name="bulk_text" rows="6" placeholder="Anita Rahayu (01)
Budi Santoso (02)
Citra Dewi (03)
Denny Firmansyah (04)
..." class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-amber-500 font-mono"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Atau Upload File CSV</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="w-full text-xs text-zinc-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-zinc-950 hover:file:brightness-110 cursor-pointer">
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-zinc-800">
                    <button type="button" @click="bulkModalOpen = false" class="px-4 py-2 rounded bg-[#141622] text-xs font-semibold text-zinc-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs shadow-md">Proses Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
