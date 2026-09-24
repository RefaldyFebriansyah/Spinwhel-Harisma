@extends('layouts.admin')

@section('title', 'Riwayat Putaran & Ekspor Log - Admin Harisma')
@section('page_title', 'Riwayat & Ekspor Log')
@section('page_subtitle', 'Catatan hasil undian peserta per mata lomba & tingkat kelas.')

@section('content')
<div x-data="{ resetModalOpen: false }">

    <!-- Top Action Bar & Filter Form -->
    <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-[#0E1017] border border-zinc-800/80 shadow-lg">
        
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.history.index') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <select name="category_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-lg bg-[#141622] border border-zinc-700/80 text-xs text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition">
                    <option value="">-- Semua Mata Lomba --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="class_level" onchange="this.form.submit()" class="px-3.5 py-2 rounded-lg bg-[#141622] border border-zinc-700/80 text-xs text-zinc-100 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition">
                    <option value="">-- Semua Tingkat Kelas --</option>
                    <option value="Kelas X" {{ request('class_level') == 'Kelas X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="Kelas XI" {{ request('class_level') == 'Kelas XI' ? 'selected' : '' }}>Kelas XI</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peserta..." class="px-3.5 py-2 rounded-lg bg-[#141622] border border-zinc-700/80 text-xs text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition">
                <button type="submit" class="p-2 rounded-lg bg-amber-500/10 text-amber-300 hover:bg-amber-500 hover:text-zinc-950 transition border border-amber-500/30">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </div>
        </form>

        <!-- Action Buttons: Export CSV, Print PDF & Reset -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('export.print-pdf') }}" target="_blank" class="px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-black text-xs transition-all shadow-lg shadow-amber-500/20 border border-amber-300/40 flex items-center space-x-2 transform hover:-translate-y-0.5 active:translate-y-0">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Surat Master PDF (Semua Mata Lomba)</span>
            </a>

            <a href="{{ route('admin.history.export', ['category_id' => request('category_id'), 'class_level' => request('class_level')]) }}" class="px-4 py-2 rounded-lg bg-[#161826] hover:bg-[#1D2033] text-amber-300 hover:text-amber-200 font-bold text-xs transition-all shadow-md border border-amber-500/30 flex items-center space-x-2 transform hover:-translate-y-0.5 active:translate-y-0">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Ekspor Tabel (CSV / Excel)</span>
            </a>

            <button @click="resetModalOpen = true" class="px-3.5 py-2 rounded-lg bg-red-950/40 hover:bg-red-900/60 text-red-300 hover:text-red-100 font-semibold text-xs transition-all border border-red-800/50 flex items-center space-x-2">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                <span>Reset Log</span>
            </button>
        </div>
    </div>

    <!-- Log Table -->
    <div class="p-5 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md space-y-4 mt-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
            <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Log Eksekusi Putaran Roda</h3>
            <span class="text-xs text-zinc-400">Total {{ $histories->total() }} catatan log</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-200">
                <thead class="bg-[#141622] text-zinc-400 uppercase text-[10px] font-bold border-b border-zinc-800">
                    <tr>
                        <th class="py-2.5 px-4"># ID</th>
                        <th class="py-2.5 px-4">Waktu Undi</th>
                        <th class="py-2.5 px-4">Mata Lomba</th>
                        <th class="py-2.5 px-4">Tingkat Kelas</th>
                        <th class="py-2.5 px-4">Nama Peserta / Hasil Undian</th>
                        <th class="py-2.5 px-4">Operator / Admin</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($histories as $log)
                        <tr class="hover:bg-zinc-800/40 transition">
                            <td class="py-3 px-4 text-zinc-500 font-mono">#{{ $log->id }}</td>
                            <td class="py-3 px-4 font-mono text-zinc-300">
                                {{ $log->spun_at ? $log->spun_at->format('d M Y - H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-amber-950 border border-amber-500/30 text-[10px] font-bold text-amber-300">
                                    {{ $log->category?->name ?? 'Solo Vocal' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $log->class_level === 'Kelas XI' ? 'bg-purple-950 text-purple-300 border border-purple-500/30' : 'bg-amber-950 text-amber-300 border border-amber-500/30' }}">
                                    {{ $log->class_level ?? 'Kelas X' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-zinc-100">
                                {{ $log->item_title }}
                            </td>
                            <td class="py-3 px-4 text-zinc-400">
                                {{ $log->executor_name }}
                            </td>
                            <!-- 3-Dots Action Menu -->
                            <td class="py-3 px-4 text-right">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" @click.outside="open = false" 
                                            class="p-1.5 rounded-lg bg-[#141622] hover:bg-zinc-800 text-zinc-300 hover:text-amber-300 transition border border-zinc-700/80 shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500/40">
                                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                    </button>

                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         x-cloak
                                         class="absolute right-0 z-30 mt-1 w-48 rounded-xl bg-[#121422] border border-zinc-700/80 shadow-2xl p-1.5 space-y-1 text-left">
                                        
                                        <a href="{{ route('export.print-pdf') }}" target="_blank" class="flex items-center space-x-2 px-3 py-1.5 text-xs font-semibold text-amber-300 hover:bg-amber-500/10 rounded-lg transition">
                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                            <span>Cetak Master PDF</span>
                                        </a>

                                        <a href="{{ route('admin.history.export', ['category_id' => $log->category_id]) }}" class="flex items-center space-x-2 px-3 py-1.5 text-xs font-semibold text-zinc-300 hover:bg-zinc-800 rounded-lg transition">
                                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                            <span>Ekspor Log CSV</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500 italic">
                                Belum ada catatan riwayat putaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-3 border-t border-zinc-800">
            {{ $histories->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div x-show="resetModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="relative w-full max-w-md bg-[#0E1017] rounded-xl border border-rose-500/50 p-6 text-zinc-100 shadow-2xl space-y-4">
            <div class="w-12 h-12 rounded-full bg-rose-950 border border-rose-500 text-rose-300 mx-auto flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>

            <div class="text-center">
                <h3 class="text-sm font-bold font-serif-ethnic text-rose-300">Konfirmasi Reset Riwayat</h3>
                <p class="text-xs text-zinc-400 mt-2">
                    Apakah Anda yakin ingin menghapus seluruh catatan riwayat putaran? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <form action="{{ route('admin.history.reset') }}" method="POST" class="flex items-center justify-center space-x-3 pt-4 border-t border-zinc-800">
                @csrf
                <button type="button" @click="resetModalOpen = false" class="px-4 py-2 rounded bg-[#141622] text-xs font-semibold text-zinc-400 hover:text-white">Batal</button>
                <button type="submit" class="px-5 py-2 rounded bg-rose-700 hover:bg-rose-600 text-white font-bold text-xs shadow-md">Ya, Hapus Semua Log</button>
            </form>
        </div>
    </div>
</div>
@endsection
