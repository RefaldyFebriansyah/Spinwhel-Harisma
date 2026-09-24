@extends('layouts.admin')

@section('title', 'Dashboard Overview - Admin Harisma')
@section('page_title', 'Dashboard Overview')
@section('page_subtitle', 'Ringkasan statistik & status sistem spinwheel Sanggar Seni Harisma')

@section('content')
<!-- Metric Cards Grid (Industry Standard Dark SaaS Cards) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- Card 1: Total Participants -->
    <div class="p-4 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Total Peserta</p>
            <h3 class="text-2xl font-black font-serif-ethnic text-amber-200 mt-1">{{ $totalItems }}</h3>
            <p class="text-[11px] text-zinc-400 mt-1"><span class="text-emerald-400 font-bold">● {{ $activeItems }}</span> aktif di roda</p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-center text-amber-400 flex-shrink-0">
            <i data-lucide="users" class="w-4 h-4"></i>
        </div>
    </div>

    <!-- Card 2: Breakdown Kelas X & XI -->
    <div class="p-4 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Pembagian Kelas</p>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-xs font-bold text-amber-300 bg-amber-950/80 px-2 py-0.5 rounded border border-amber-500/30">Kelas X: {{ $itemsKelasX }}</span>
                <span class="text-xs font-bold text-purple-300 bg-purple-950/80 px-2 py-0.5 rounded border border-purple-500/30">Kelas XI: {{ $itemsKelasXI }}</span>
            </div>
            <p class="text-[11px] text-zinc-400 mt-1">Status: Siap Diundi</p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-center text-amber-400 flex-shrink-0">
            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
        </div>
    </div>

    <!-- Card 3: Today's Spins -->
    <div class="p-4 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Putaran Hari Ini</p>
            <h3 class="text-2xl font-black font-serif-ethnic text-amber-200 mt-1">{{ $spinsToday }}</h3>
            <p class="text-[11px] text-zinc-400 mt-1">Kumulatif: {{ $totalSpins }} spin</p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-center text-amber-400 flex-shrink-0">
            <i data-lucide="rotate-cw" class="w-4 h-4"></i>
        </div>
    </div>

    <!-- Card 4: Spin Engine Duration -->
    <div class="p-4 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Durasi Putaran</p>
            <h3 class="text-2xl font-black font-serif-ethnic text-amber-200 mt-1">{{ $engineConfig['spin_duration'] }}s</h3>
            <p class="text-[11px] text-zinc-400 mt-1">Min {{ $engineConfig['min_rotations'] }}x rotasi</p>
        </div>
        <div class="w-9 h-9 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-center text-amber-400 flex-shrink-0">
            <i data-lucide="timer" class="w-4 h-4"></i>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">

    <!-- Categories Status Breakdown -->
    <div class="lg:col-span-1 p-5 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
            <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Cabang Mata Lomba</h3>
            <a href="{{ route('admin.items.index') }}" class="text-xs text-amber-400 hover:underline font-semibold">Kelola Peserta</a>
        </div>

        <div class="space-y-2">
            @foreach($categories as $cat)
                <div class="p-3 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-between hover:border-amber-500/40 transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-7 h-7 rounded bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                            <i data-lucide="{{ $cat->icon }}" class="w-3.5 h-3.5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-zinc-100">{{ $cat->name }}</h4>
                            <p class="text-[10px] text-zinc-400">{{ $cat->active_wheel_items_count }} aktif dari {{ $cat->wheel_items_count }} total</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.items.index', ['category_id' => $cat->id]) }}" class="px-2.5 py-1 rounded bg-amber-500/20 text-xs text-amber-300 border border-amber-500/40 hover:bg-amber-500 hover:text-zinc-950 font-bold transition">
                        Kelola
                    </a>
                </div>
            @endforeach
        </div>

        <div class="pt-3 border-t border-zinc-800">
            <a href="{{ route('stage') }}" target="_blank" class="w-full py-2.5 rounded bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs flex items-center justify-center space-x-2 transition shadow-md">
                <i data-lucide="play-circle" class="w-4 h-4"></i>
                <span>TAMPILKAN LAYAR PANGGUNG</span>
            </a>
        </div>
    </div>

    <!-- Recent Spin History Table -->
    <div class="lg:col-span-2 p-5 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
            <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Riwayat Putaran Terakhir</h3>
            <a href="{{ route('admin.history.index') }}" class="text-xs text-amber-400 hover:underline font-semibold">Lihat Semua Log</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-200">
                <thead class="bg-[#141622] text-zinc-400 uppercase text-[10px] font-bold border-b border-zinc-800">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Mata Lomba</th>
                        <th class="py-2.5 px-3">Kelas</th>
                        <th class="py-2.5 px-3">Hasil Undian</th>
                        <th class="py-2.5 px-3">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60">
                    @forelse($recentSpins as $spin)
                        <tr class="hover:bg-zinc-800/40 transition">
                            <td class="py-2.5 px-3 text-zinc-400 font-mono text-[11px]">{{ $spin->spun_at ? $spin->spun_at->format('H:i:s') : '-' }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded bg-amber-950 text-[10px] font-bold text-amber-300 border border-amber-500/30">
                                    {{ $spin->category?->name ?? 'Solo Vocal' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-amber-300">{{ $spin->class_level ?? 'Kelas X' }}</td>
                            <td class="py-2.5 px-3 font-bold text-zinc-100">{{ $spin->item_title }}</td>
                            <td class="py-2.5 px-3 text-zinc-400">{{ $spin->executor_name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-zinc-500 italic">Belum ada riwayat putaran tercatat hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
