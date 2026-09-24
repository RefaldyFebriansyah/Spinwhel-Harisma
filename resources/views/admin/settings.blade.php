@extends('layouts.admin')

@section('title', 'Pengaturan Mesin Spin & Branding - Admin Harisma')
@section('page_title', 'Pengaturan Mesin Spin')
@section('page_subtitle', 'Konfigurasi fisika putaran, mode pemenang, & branding visual.')

@section('content')
<div class="max-w-4xl space-y-6">

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Section 1: Kontrol Mekanisme Putaran (Engine Control) -->
        <div class="p-5 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md space-y-5">
            <div class="flex items-center space-x-3 pb-3 border-b border-zinc-800">
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <i data-lucide="cpu" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Mekanisme Putaran (Engine Control)</h3>
                    <p class="text-xs text-zinc-400">Pengaturan durasi, gaya perlambatan, & rotasi roda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Spin Duration Slider -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-zinc-300">Durasi Putaran (Detik)</label>
                        <span class="text-xs font-mono font-bold text-amber-300" id="durationValue">{{ $engineConfig['spin_duration'] }}s</span>
                    </div>
                    <input type="range" name="spin_duration" min="2" max="15" step="0.5" value="{{ $engineConfig['spin_duration'] }}" 
                           oninput="document.getElementById('durationValue').innerText = this.value + 's'"
                           class="w-full accent-amber-500 cursor-pointer">
                    <p class="text-[11px] text-zinc-500 mt-1">Lama waktu roda berputar hingga berhenti sempurna (rekomendasi: 5s)</p>
                </div>

                <!-- Minimum Rotations Slider -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-zinc-300">Jumlah Putaran Minimum (Rotasi)</label>
                        <span class="text-xs font-mono font-bold text-amber-300" id="rotationsValue">{{ $engineConfig['min_rotations'] }}x</span>
                    </div>
                    <input type="range" name="min_rotations" min="3" max="15" step="1" value="{{ $engineConfig['min_rotations'] }}" 
                           oninput="document.getElementById('rotationsValue').innerText = this.value + 'x'"
                           class="w-full accent-amber-500 cursor-pointer">
                    <p class="text-[11px] text-zinc-500 mt-1">Jumlah putaran 360&deg; penuh sebelum mendarat di pemenang</p>
                </div>

                <!-- Easing Curve Picker -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Tipe Easing (Deceleration Curve)</label>
                    <select name="easing_type" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                        <option value="cubic-ease-out" {{ $engineConfig['easing_type'] == 'cubic-ease-out' ? 'selected' : '' }}>Smooth Deceleration (Cubic Ease Out - Recommended)</option>
                        <option value="quad-ease-out" {{ $engineConfig['easing_type'] == 'quad-ease-out' ? 'selected' : '' }}>Quadratic Ease Out (Standard)</option>
                        <option value="exponential" {{ $engineConfig['easing_type'] == 'exponential' ? 'selected' : '' }}>Dramatic Sudden Stop (Exponential)</option>
                    </select>
                </div>

                <!-- Auto Remove Winner Mode -->
                <div class="p-3.5 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-200">Mode "Auto Remove Winner"</h4>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Otomatis menonaktifkan item pemenang dari roda</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="auto_remove_winner" value="1" {{ $engineConfig['auto_remove_winner'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 2: Customization Branding & Sound -->
        <div class="p-5 rounded-xl bg-[#0E1017] border border-zinc-800 shadow-md space-y-5">
            <div class="flex items-center space-x-3 pb-3 border-b border-zinc-800">
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <i data-lucide="palette" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-serif-ethnic text-amber-200">Kustomisasi Tampilan & Suara</h3>
                    <p class="text-xs text-zinc-400">Judul event, intensitas motif batik, & efek audio</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Event Title -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Judul Event / Sanggar</label>
                    <input type="text" name="event_title" value="{{ $engineConfig['event_title'] }}" required class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <!-- Sub-Title / Tagline -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1">Sub-Judul / Tagline Event</label>
                    <input type="text" name="sub_title" value="{{ $engineConfig['sub_title'] }}" class="w-full px-3 py-2 rounded bg-[#141622] border border-zinc-800 text-xs text-zinc-100 focus:outline-none focus:border-amber-500">
                </div>

                <!-- Batik Pattern Opacity Slider -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-semibold text-zinc-300">Intensitas Motif Batik Latar</label>
                        <span class="text-xs font-mono font-bold text-amber-300" id="opacityValue">{{ $engineConfig['batik_pattern_opacity'] }}</span>
                    </div>
                    <input type="range" name="batik_pattern_opacity" min="0" max="0.5" step="0.05" value="{{ $engineConfig['batik_pattern_opacity'] }}" 
                           oninput="document.getElementById('opacityValue').innerText = this.value"
                           class="w-full accent-amber-500 cursor-pointer">
                    <p class="text-[11px] text-zinc-500 mt-1">Tingkat transparansi aksen batik di latar panggung</p>
                </div>

                <!-- Sound Effects Enable Toggle -->
                <div class="p-3.5 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-zinc-200">Efek Suara (Web Audio SFX)</h4>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Suara tick putaran & fanfare pemenang</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="sound_enabled" value="1" {{ $engineConfig['sound_enabled'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button Bar -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-black text-xs shadow-lg shadow-amber-500/20 border border-amber-300/40 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center space-x-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>SIMPAN PENGATURAN MESIN</span>
            </button>
        </div>
    </form>
</div>
@endsection
