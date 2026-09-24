@extends('layouts.app')

@section('title', $engineConfig['event_title'] . ' - Stage Spinwheel Harisma')

@section('content')
<!-- Locked 100vh container: Absolutely NO vertical page scrolling! -->
<div x-data="spinwheelApp()" x-init="init()" class="h-screen w-screen overflow-hidden flex flex-col justify-between relative bg-[#08090D] text-zinc-100 font-sans select-none">

    <!-- Subtle Batik Overlay Texture Background -->
    <div class="fixed inset-0 pointer-events-none z-0 opacity-10 bg-batik-pattern bg-repeat"></div>

    <!-- Decorative Top Corner Ornaments -->
    <div class="absolute top-0 left-0 w-32 h-32 opacity-20 pointer-events-none z-10">
        <svg viewBox="0 0 100 100" fill="none" stroke="#D4AF37" stroke-width="1.2">
            <path d="M0 0 C40 0 60 20 60 60 C20 60 0 40 0 0 Z M15 15 C35 15 45 25 45 45 M0 0 L70 70" />
            <circle cx="25" cy="25" r="3.5" fill="#D4AF37"/>
        </svg>
    </div>
    <div class="absolute top-0 right-0 w-32 h-32 opacity-20 pointer-events-none z-10 transform scale-x-[-1]">
        <svg viewBox="0 0 100 100" fill="none" stroke="#D4AF37" stroke-width="1.2">
            <path d="M0 0 C40 0 60 20 60 60 C20 60 0 40 0 0 Z M15 15 C35 15 45 25 45 45 M0 0 L70 70" />
            <circle cx="25" cy="25" r="3.5" fill="#D4AF37"/>
        </svg>
    </div>

    <!-- 1. Top Header Bar (Prominent & High-Contrast for Projector/Stage Display) -->
    <header class="relative z-20 w-full bg-[#0B0C12]/95 backdrop-blur-md border-b border-amber-500/20 px-6 py-3 md:py-3.5 flex items-center justify-between flex-shrink-0 shadow-2xl">
        
        <!-- Brand & School Info -->
        <div class="flex items-center space-x-3.5">
            <img src="{{ asset('images/logo-harisma.png') }}" alt="Logo Sanggar Seni Harisma" class="w-11 h-11 md:w-12 md:h-12 object-contain drop-shadow-[0_0_12px_rgba(212,175,55,0.7)]">
            <div>
                <div class="flex items-center space-x-2.5">
                    <h1 class="text-sm md:text-base font-black tracking-wider uppercase font-serif-ethnic text-amber-200">
                        SANGGAR SENI HARISMA
                    </h1>
                    <span class="text-[10px] md:text-xs font-extrabold px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/50 text-amber-300">
                        SMKN 1 CIAMIS
                    </span>
                </div>
                <p class="text-xs text-zinc-300 font-medium mt-0.5">
                    Lomba Memperingati HUT Harisma
                </p>
            </div>
        </div>

        <!-- Category Navigation Tabs (Prominent & Easy to See from Distance) -->
        <nav class="flex items-center space-x-2 bg-[#121422] p-1.5 rounded-xl border border-amber-500/20 shadow-inner">
            @foreach($categories as $cat)
                <button @click="switchCategory('{{ $cat->slug }}')"
                        :class="activeSlug === '{{ $cat->slug }}' 
                            ? 'bg-gradient-to-r from-amber-500/30 to-amber-600/30 text-amber-200 border-amber-400 font-black shadow-[0_0_15px_rgba(245,158,11,0.25)] scale-[1.02]' 
                            : 'text-zinc-400 hover:text-zinc-100 border-transparent hover:bg-white/5 font-bold'"
                        class="px-4 py-2 rounded-lg text-xs md:text-sm border transition-all duration-150 flex items-center space-x-2">
                    <i data-lucide="{{ $cat->icon }}" class="w-4 h-4 text-amber-400"></i>
                    <span>{{ $cat->name }}</span>
                </button>
            @endforeach
        </nav>

        <!-- Right Side Widgets & Actions -->
        <div class="flex items-center space-x-3">
            <!-- WIB Live Clock Widget -->
            <div class="flex items-center space-x-2 px-3.5 py-1.5 rounded-xl bg-[#121422] border border-amber-500/20 text-xs md:text-sm text-amber-300 font-mono font-bold shadow-sm">
                <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
                <span x-text="currentTime"></span>
                <span class="font-extrabold text-[10px] bg-amber-500/20 px-1.5 py-0.5 rounded text-amber-300">WIB</span>
            </div>

            <!-- Fullscreen Button -->
            <button @click="toggleFullscreen()" class="p-2 rounded-xl bg-[#121422] hover:bg-amber-500/20 border border-amber-500/20 text-amber-300 transition shadow-sm" title="Layar Penuh">
                <i data-lucide="maximize" class="w-4 h-4"></i>
            </button>

            <!-- Panel Juri / Admin Button -->
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 border border-amber-500/40 text-xs md:text-sm font-bold flex items-center space-x-2 transition shadow-md">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Panel Juri</span>
            </a>
        </div>
    </header>

    <!-- 2. Sub-Header Banner + Class Filter Tabs (Kelas X & Kelas XI) -->
    <section class="relative z-20 max-w-7xl w-full mx-auto px-6 pt-2 pb-1 flex-shrink-0">
        <div class="flex items-center justify-between border-b border-zinc-800/80 pb-2">
            <div>
                <h2 class="text-lg md:text-xl font-black font-serif-ethnic uppercase tracking-widest text-amber-200" x-text="categoryName.toUpperCase()"></h2>
            </div>

            <!-- Dedicated Class Switcher Buttons -->
            <div class="flex items-center space-x-1.5 bg-[#12141F] p-1 rounded-lg border border-zinc-800">
                <span class="text-[11px] text-zinc-400 font-semibold px-1.5">Putar Kelas:</span>
                <button @click="switchClass('Kelas X')"
                        :class="selectedClass === 'Kelas X' 
                            ? 'bg-amber-500 text-zinc-950 font-black border-amber-400' 
                            : 'text-zinc-400 hover:text-white bg-transparent border-transparent'"
                        class="px-3 py-1 rounded text-xs transition uppercase font-bold border flex items-center space-x-1">
                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                    <span>Kelas X</span>
                </button>
                <button @click="switchClass('Kelas XI')"
                        :class="selectedClass === 'Kelas XI' 
                            ? 'bg-amber-500 text-zinc-950 font-black border-amber-400' 
                            : 'text-zinc-400 hover:text-white bg-transparent border-transparent'"
                        class="px-3 py-1 rounded text-xs transition uppercase font-bold border flex items-center space-x-1">
                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                    <span>Kelas XI</span>
                </button>
            </div>
        </div>
    </section>

    <!-- 3. Main Stage Arena Grid -->
    <main class="relative z-20 flex-1 max-w-7xl w-full mx-auto px-6 py-2 grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch overflow-hidden">

        <!-- LEFT BOX: RODA GILIRAN PENTAS (7 Cols) -->
        <div class="lg:col-span-7 bg-[#0E1017] rounded-xl border border-zinc-800 p-4 relative shadow-xl flex flex-col justify-between items-center h-full overflow-hidden">
            
            <!-- Traditional Gold Corner Trims (✦) -->
            <span class="absolute top-1.5 left-2 text-amber-500/60 text-xs font-serif">✦</span>
            <span class="absolute top-1.5 right-2 text-amber-500/60 text-xs font-serif">✦</span>
            <span class="absolute bottom-1.5 left-2 text-amber-500/60 text-xs font-serif">✦</span>
            <span class="absolute bottom-1.5 right-2 text-amber-500/60 text-xs font-serif">✦</span>

            <!-- Box Header -->
            <div class="w-full flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="text-amber-400 text-xs">✦</span>
                    <h3 class="text-xs font-bold tracking-widest text-amber-200 uppercase font-serif-ethnic">
                        RODA GILIRAN PENTAS (<span x-text="selectedClass"></span>)
                    </h3>
                </div>
                <div class="px-2.5 py-0.5 rounded bg-[#141622] border border-zinc-800 text-xs text-amber-400 font-mono">
                    Putaran Ke- <span x-text="spinCount" class="font-bold text-white"></span>
                </div>
            </div>

            <!-- Wheel Canvas Wrapper & Pointer -->
            <div class="relative my-auto flex flex-col items-center justify-center flex-1">

                <!-- Pointer Pin -->
                <div class="z-30 -mb-4 flex flex-col items-center filter drop-shadow-[0_4px_8px_rgba(0,0,0,0.9)]">
                    <svg width="32" height="40" viewBox="0 0 40 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 50 L4 18 C0 10 6 0 20 0 C34 0 40 10 36 18 L20 50 Z" fill="url(#goldPinGrad)" stroke="#FFE893" stroke-width="1.5"/>
                        <circle cx="20" cy="14" r="4.5" fill="#11131C" stroke="#D4AF37" stroke-width="1.5"/>
                        <defs>
                            <linearGradient id="goldPinGrad" x1="0" y1="0" x2="40" y2="50" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FFF5C0"/>
                                <stop offset="0.5" stop-color="#D4AF37"/>
                                <stop offset="1" stop-color="#8C6D13"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <!-- Canvas Outer Gold Rim -->
                <div class="p-1.5 rounded-full border-2 border-amber-500/40 bg-[#08090D] shadow-2xl relative flex items-center justify-center">
                    <canvas id="wheelCanvas" width="380" height="380" class="max-w-[75vw] max-h-[34vh] md:max-w-[340px] md:max-h-[340px] lg:max-w-[360px] lg:max-h-[360px] rounded-full cursor-pointer transition-transform duration-300 hover:scale-[1.005]" @click="spin()"></canvas>

                    <!-- Center Hub Button with Text "PUTAR" -->
                    <button @click="spin()" 
                            :disabled="isSpinning || items.length === 0"
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-16 h-16 md:w-18 md:h-18 rounded-full bg-gradient-to-b from-amber-400 via-yellow-500 to-amber-700 p-0.5 shadow-2xl hover:scale-105 active:scale-95 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed z-20 flex items-center justify-center border border-amber-300">
                        <div class="w-full h-full rounded-full bg-[#0E1017] flex items-center justify-center border border-amber-500/50">
                            <span class="text-[11px] font-black tracking-widest text-amber-200 uppercase font-mono" x-text="isSpinning ? 'PUTAR...' : 'PUTAR'"></span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Prominent Wide Golden Spin Button -->
            <div class="w-full mt-2 flex-shrink-0">
                <button @click="spin()" 
                        :disabled="isSpinning || items.length === 0"
                        class="w-full py-2.5 px-6 rounded bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs md:text-sm tracking-widest uppercase shadow-md active:scale-98 transition duration-200 disabled:opacity-50 flex items-center justify-center space-x-2 border border-amber-400">
                    <i data-lucide="play" class="w-4 h-4 fill-current"></i>
                    <span x-text="isSpinning ? 'MENGUNDI PESERTA ' + selectedClass.toUpperCase() + '...' : 'PUTAR RODA UNDIAN (' + selectedClass.toUpperCase() + ')'"></span>
                </button>
            </div>

            <!-- Bottom Control Checkboxes -->
            <div class="w-full flex items-center justify-between mt-2 px-1 text-[11px] text-zinc-400 border-t border-zinc-800 pt-2 flex-shrink-0">
                <label class="flex items-center space-x-1.5 cursor-pointer hover:text-zinc-200">
                    <input type="checkbox" x-model="autoRemoveWinner" class="rounded bg-zinc-900 border-amber-500/40 text-amber-500 focus:ring-amber-500/50">
                    <span>Hapus peserta terpilih</span>
                </label>

                <label class="flex items-center space-x-1.5 cursor-pointer hover:text-zinc-200">
                    <input type="checkbox" x-model="soundEnabled" class="rounded bg-zinc-900 border-amber-500/40 text-amber-500 focus:ring-amber-500/50">
                    <span>Efek suara</span>
                </label>
            </div>
        </div>

        <!-- RIGHT BOX: URUTAN TAMPIL PESERTA (5 Cols) -->
        <div class="lg:col-span-5 bg-[#0E1017] rounded-xl border border-zinc-800 p-4 relative shadow-xl flex flex-col justify-between h-full overflow-hidden">
            
            <!-- Traditional Gold Corner Trims (✦) -->
            <span class="absolute top-1.5 left-2 text-amber-500/60 text-xs font-serif">✦</span>
            <span class="absolute top-1.5 right-2 text-amber-500/60 text-xs font-serif">✦</span>
            <span class="absolute bottom-1.5 left-2 text-amber-500/60 text-xs font-serif">✦</span>
            <span class="absolute bottom-1.5 right-2 text-amber-500/60 text-xs font-serif">✦</span>

            <div class="flex flex-col h-full overflow-hidden">
                <!-- Box Header & Counter -->
                <div class="flex items-start justify-between border-b border-zinc-800 pb-2 mb-2 flex-shrink-0">
                    <div>
                        <h3 class="text-xs font-bold tracking-widest text-amber-200 uppercase font-serif-ethnic">
                            URUTAN TAMPIL PESERTA
                        </h3>
                        <p class="text-[10px] text-zinc-400 mt-0.5">
                            Daftar urutan pentas (<span x-text="selectedClass"></span>)
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-xs px-2.5 py-0.5 rounded bg-[#141622] border border-zinc-800 text-amber-300 font-mono font-bold"
                              x-text="currentDrawnList.length + ' / ' + items.length + ' Terundi'"></span>
                    </div>
                </div>

                <!-- Action Bar Buttons (Master Combined Export & Print Buttons) -->
                <div class="flex items-center justify-between space-x-1.5 mb-2 flex-shrink-0">
                    <button @click="resetDrawnSequence()" 
                            class="px-2 py-1.5 rounded bg-[#141622] hover:bg-zinc-800 border border-zinc-800 text-[11px] text-amber-300 font-semibold transition flex items-center space-x-1">
                        <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                        <span>Acak Ulang</span>
                    </button>
                    
                    <div class="flex items-center space-x-1">
                        <button @click="printPDF()" 
                                class="px-2.5 py-1.5 rounded bg-[#141622] hover:bg-amber-500/20 border border-amber-500/30 text-[11px] text-amber-300 font-bold transition flex items-center space-x-1">
                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                            <span>Cetak PDF</span>
                        </button>

                        <button @click="exportMasterCSV()" 
                                class="px-2.5 py-1.5 rounded bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-[11px] text-amber-300 font-bold transition flex items-center space-x-1 shadow-sm" title="Ekspor CSV Gabungan Selang-Seling X & XI">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Ekspor Master CSV</span>
                        </button>
                    </div>
                </div>

                <!-- Harisma Shield Watermark Overlay + Sequence List -->
                <div class="relative flex-1 overflow-y-auto pr-1 my-1 min-h-0">
                    
                    <!-- Clear Harisma Logo Watermark Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-25 z-0">
                        <img src="{{ asset('images/logo-harisma.png') }}" alt="Harisma Watermark" class="w-64 h-64 object-contain filter drop-shadow-[0_0_15px_rgba(212,175,55,0.4)]">
                    </div>

                    <!-- Empty State -->
                    <div x-show="currentDrawnList.length === 0" class="relative z-10 flex flex-col items-center justify-center h-full text-center py-8">
                        <div class="w-10 h-10 rounded-lg bg-[#141622] border border-zinc-800 flex items-center justify-center text-amber-400 mb-2.5 shadow-md">
                            <i data-lucide="list-ordered" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-amber-200">
                            BELUM ADA NOMOR TAMPIL
                        </h4>
                        <p class="text-[11px] text-zinc-300 max-w-xs mt-1 leading-relaxed bg-[#141622]/80 p-2 rounded border border-zinc-800 backdrop-blur-sm">
                            Tekan <span class="text-amber-300 font-bold">"PUTAR RODA UNDIAN"</span> untuk mengacak urutan tampil <span x-text="selectedClass"></span>.
                        </p>
                    </div>

                    <!-- Drawn Sequence List Items -->
                    <div x-show="currentDrawnList.length > 0" class="relative z-10 space-y-1.5">
                        <template x-for="(winner, idx) in currentDrawnList" :key="winner.id || idx">
                            <div class="p-2 rounded bg-[#141622]/90 backdrop-blur-sm border border-zinc-800 flex items-center justify-between shadow-sm hover:border-amber-500/40 transition">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div class="w-6 h-6 rounded font-mono font-black text-xs flex items-center justify-center bg-amber-500 text-zinc-950 flex-shrink-0 shadow-sm">
                                        <span x-text="idx + 1"></span>
                                    </div>
                                    <div class="truncate">
                                        <h5 class="text-xs font-bold text-zinc-100 truncate" x-text="winner.item_title"></h5>
                                        <p class="text-[10px] text-amber-300/80 truncate mt-0.5" x-text="(winner.subtitle ? winner.subtitle + ' &bull; ' : '') + winner.class_level"></p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 pl-2">
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#08090D] text-amber-300 border border-zinc-800 font-bold" x-text="'No. ' + (idx + 1)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Bottom Status Footer Bar -->
            <div class="border-t border-zinc-800 pt-2 mt-1 flex items-center justify-between text-[10px] text-zinc-400 flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span x-text="isSpinning ? 'Sedang pengundian giliran pentas...' : 'Siap mengundi giliran pentas ke panggung'"></span>
                </div>
                <span class="font-mono text-[10px] text-zinc-500">
                    Sistem Tervalidasi <span class="text-amber-400/80">#HARISMA-2026</span>
                </span>
            </div>
        </div>

    </main>

    <!-- Footer Copyright Bar -->
    <footer class="relative z-20 w-full border-t border-zinc-800 bg-[#08090D] px-6 py-1.5 text-center text-[11px] text-zinc-500 flex-shrink-0">
        Sanggar Seni Harisma &bull; SMKN 1 Ciamis &copy; 2026
    </footer>

    <!-- POST-SPIN CELEBRATION OVERLAY MODAL -->
    <div x-show="showCelebrationModal" x-cloak 
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        
        <div class="relative w-full max-w-md bg-[#0E1017] rounded-xl border border-amber-500/40 p-6 text-zinc-100 shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-mono font-bold uppercase tracking-wider">
                        HASIL PENGUNDIAN RESMI (<span x-text="selectedClass"></span>)
                    </span>
                </div>
                <button @click="showCelebrationModal = false" class="text-zinc-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-1">
                <h3 class="text-lg font-bold font-serif-ethnic text-amber-200">
                    Selamat & Semangat Lombanya! 🎉
                </h3>
                <p class="text-xs text-zinc-300 leading-relaxed">
                    Pengundian nomor urut pentas untuk <span class="text-amber-300 font-bold" x-text="selectedClass + ' - ' + categoryName"></span> telah berhasil diacak secara transparan dan adil!
                </p>
            </div>

            <!-- Preview Top Drawn Participants -->
            <div class="bg-[#141622] rounded-lg border border-zinc-800 p-3 space-y-1.5 text-left">
                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400">3 Peserta Tampil Pertama (<span x-text="selectedClass"></span>):</p>
                <template x-for="(item, idx) in currentDrawnList.slice(0, 3)" :key="item.id || idx">
                    <div class="flex items-center justify-between p-1.5 rounded bg-[#08090D] border border-zinc-800 text-xs">
                        <div class="flex items-center space-x-2 min-w-0">
                            <span class="w-5 h-5 rounded bg-amber-500 text-zinc-950 font-black text-[11px] flex items-center justify-center flex-shrink-0" x-text="idx + 1"></span>
                            <span class="font-semibold text-zinc-100 truncate" x-text="item.item_title"></span>
                        </div>
                        <span class="text-[10px] font-mono text-amber-300 font-bold flex-shrink-0 pl-2" x-text="item.subtitle || item.class_level"></span>
                    </div>
                </template>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex items-center justify-end space-x-2">
                <button @click="printPDF()" 
                        class="px-3 py-2 rounded bg-[#141622] hover:bg-zinc-800 border border-zinc-800 text-xs text-amber-300 font-bold transition flex items-center space-x-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak PDF</span>
                </button>
                <button @click="exportMasterCSV()" 
                        class="px-3 py-2 rounded bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-xs transition shadow-md flex items-center space-x-1.5">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Ekspor Master CSV (Acak X & XI)</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function spinwheelApp() {
        return {
            activeSlug: '{{ $activeCategory?->slug ?? "solo-vocal" }}',
            categoryName: '{{ $activeCategory?->name ?? "Solo Vocal" }}',
            categoryId: {{ $activeCategory?->id ?? 1 }},
            selectedClass: 'Kelas X',
            
            items: [],
            drawnWinnersX: [],
            drawnWinnersXI: [],
            spinCount: 1,

            spinDuration: {{ $engineConfig['spin_duration'] ?? 5 }},
            minRotations: {{ $engineConfig['min_rotations'] ?? 6 }},
            autoRemoveWinner: {{ ($engineConfig['auto_remove_winner'] ?? false) ? 'true' : 'true' }},
            soundEnabled: {{ ($engineConfig['sound_enabled'] ?? true) ? 'true' : 'true' }},

            canvas: null,
            ctx: null,
            currentAngle: 0,
            isSpinning: false,
            showCelebrationModal: false,
            currentTime: '',

            get currentDrawnList() {
                if (this.selectedClass === 'Kelas X') {
                    return this.drawnWinnersX;
                } else {
                    return this.drawnWinnersXI;
                }
            },

            init() {
                this.canvas = document.getElementById('wheelCanvas');
                this.ctx = this.canvas.getContext('2d');
                
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);

                this.fetchCategoryItems(this.activeSlug, this.selectedClass);
            },

            updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                this.currentTime = `${hours}:${minutes}`;
            },

            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            },

            async switchCategory(slug) {
                if (this.isSpinning) return;
                this.activeSlug = slug;
                this.drawnWinnersX = [];
                this.drawnWinnersXI = [];
                this.spinCount = 1;
                this.showCelebrationModal = false;
                await this.fetchCategoryItems(slug, this.selectedClass);
            },

            async switchClass(classLevel) {
                if (this.isSpinning) return;
                this.selectedClass = classLevel;
                this.showCelebrationModal = false;
                await this.fetchCategoryItems(this.activeSlug, classLevel);
            },

            async fetchCategoryItems(slug, classLevel) {
                try {
                    const res = await fetch(`/api/categories/${slug}/items?class_level=${encodeURIComponent(classLevel)}`);
                    const data = await res.json();
                    if (data.success) {
                        this.categoryId = data.category.id;
                        this.categoryName = data.category.name;
                        this.items = data.items;
                        this.drawWheel();
                    }
                } catch (e) {
                    console.error('Error fetching wheel items:', e);
                }
            },

            drawWheel() {
                if (!this.ctx) return;
                
                const width = this.canvas.width;
                const height = this.canvas.height;
                const centerX = width / 2;
                const centerY = height / 2;
                const radius = width / 2 - 10;
                const numSegments = this.items.length;

                this.ctx.clearRect(0, 0, width, height);

                if (numSegments === 0) {
                    this.drawEmptyWheelState(centerX, centerY, radius);
                    return;
                }

                const arcSize = (2 * Math.PI) / numSegments;

                // Palette Colors for Wheel Segments
                const segmentColors = [
                    '#8C2D19', // Terracotta Red
                    '#255FA6', // Royal Blue
                    '#B87314', // Deep Gold/Bronze
                    '#6B21A8', // Royal Purple
                    '#15803D', // Emerald Green
                    '#C2410C', // Burnt Orange
                    '#0F766E', // Deep Teal
                    '#A16207'  // Antique Gold
                ];

                // Dynamic Font Scaling
                let fontSize = 12;
                if (numSegments > 28) fontSize = 7.5;
                else if (numSegments > 20) fontSize = 8.5;
                else if (numSegments > 14) fontSize = 10;

                // Draw Slices
                for (let i = 0; i < numSegments; i++) {
                    const item = this.items[i];
                    const angle = this.currentAngle + i * arcSize;

                    // Slice Path
                    this.ctx.beginPath();
                    this.ctx.moveTo(centerX, centerY);
                    this.ctx.arc(centerX, centerY, radius, angle, angle + arcSize);
                    this.ctx.closePath();

                    // Slice Fill Color
                    this.ctx.fillStyle = item.color || segmentColors[i % segmentColors.length];
                    this.ctx.fill();

                    // Gold Border Divider
                    this.ctx.lineWidth = 1.2;
                    this.ctx.strokeStyle = '#FFE893';
                    this.ctx.stroke();

                    // Slice Label Text
                    this.ctx.save();
                    this.ctx.translate(centerX, centerY);
                    this.ctx.rotate(angle + arcSize / 2);
                    this.ctx.textAlign = 'right';
                    this.ctx.fillStyle = item.text_color || '#FFFFFF';
                    this.ctx.font = `bold ${fontSize}px "Plus Jakarta Sans", sans-serif`;
                    this.ctx.shadowColor = 'rgba(0,0,0,0.85)';
                    this.ctx.shadowBlur = 3;

                    let label = item.title;
                    const maxChars = numSegments > 20 ? 18 : 24;
                    if (label.length > maxChars) {
                        label = label.substring(0, maxChars - 2) + '..';
                    }

                    this.ctx.fillText(label, radius - 14, fontSize / 3);
                    this.ctx.restore();
                }

                // Outer Gold Ring
                this.ctx.beginPath();
                this.ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                this.ctx.lineWidth = 3.5;
                this.ctx.strokeStyle = '#D4AF37';
                this.ctx.stroke();
            },

            drawEmptyWheelState(centerX, centerY, radius) {
                this.ctx.beginPath();
                this.ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
                this.ctx.fillStyle = '#141622';
                this.ctx.fill();
                this.ctx.lineWidth = 2;
                this.ctx.strokeStyle = '#D4AF37';
                this.ctx.stroke();

                this.ctx.font = 'bold 13px "Plus Jakarta Sans", sans-serif';
                this.ctx.fillStyle = '#D4AF37';
                this.ctx.textAlign = 'center';
                this.ctx.fillText('SEMUA PESERTA TELAH TERUNDI', centerX, centerY - 8);
                
                this.ctx.font = '10px "Plus Jakarta Sans", sans-serif';
                this.ctx.fillStyle = '#A1A1AA';
                this.ctx.fillText('Tekan "Acak Ulang" untuk mengundi kembali.', centerX, centerY + 10);
            },

            spin() {
                if (this.isSpinning || this.items.length === 0) return;

                this.isSpinning = true;

                // Shuffle active items for selected class
                let shuffled = [...this.items];
                for (let i = shuffled.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
                }

                const numSegments = this.items.length;
                const arcSize = (2 * Math.PI) / numSegments;
                const extraSpins = this.minRotations * 2 * Math.PI + (Math.random() * 2 * Math.PI);
                const finalAngle = this.currentAngle + extraSpins;

                const startTime = performance.now();
                const durationMs = this.spinDuration * 1000;
                const startAngle = this.currentAngle;

                let lastAudioTickAngle = startAngle;

                const animate = (now) => {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / durationMs, 1);
                    
                    // Smooth Cubic Ease Out
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    this.currentAngle = startAngle + (finalAngle - startAngle) * easeOut;

                    // Audio Tick Sound
                    if (this.soundEnabled && Math.abs(this.currentAngle - lastAudioTickAngle) >= arcSize) {
                        this.playTickSound();
                        lastAudioTickAngle = this.currentAngle;
                    }

                    this.drawWheel();

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        this.isSpinning = false;
                        if (this.soundEnabled) {
                            this.playChimeSound();
                        }
                        this.handleSpinComplete(shuffled);
                    }
                };

                requestAnimationFrame(animate);
            },

            async handleSpinComplete(shuffledList) {
                const drawnList = shuffledList.map((item, idx) => ({
                    id: item.id,
                    item_title: item.title,
                    subtitle: item.subtitle || item.class_level,
                    class_level: item.class_level,
                    category: this.categoryName,
                    drawn_at: new Date().toLocaleTimeString('id-ID')
                }));

                if (this.selectedClass === 'Kelas X') {
                    this.drawnWinnersX = drawnList;
                } else {
                    this.drawnWinnersXI = drawnList;
                }

                this.spinCount++;

                const titlesSummary = shuffledList.map((item, idx) => `Urutan ${idx + 1}: ${item.title} (${item.subtitle || item.class_level})`).join(' | ');

                try {
                    await fetch('/api/spin/record', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            category_id: this.categoryId,
                            wheel_item_id: shuffledList[0] ? shuffledList[0].id : null,
                            item_title: `Acak Undian ${this.categoryName} (${this.selectedClass})`,
                            class_level: this.selectedClass,
                            notes: titlesSummary,
                            executor: 'Panitia Stage'
                        })
                    });
                } catch (e) {
                    console.error('Error saving randomized sequence:', e);
                }

                // Show Celebration Modal Overlay
                this.showCelebrationModal = true;
            },

            resetDrawnSequence() {
                if (this.isSpinning) return;
                if (this.selectedClass === 'Kelas X') {
                    this.drawnWinnersX = [];
                } else {
                    this.drawnWinnersXI = [];
                }
                this.spinCount = 1;
                this.showCelebrationModal = false;
                this.fetchCategoryItems(this.activeSlug, this.selectedClass);
            },

            // MASTER INTERLEAVED EXPORT: Combines Rank 1 of X, Rank 1 of XI, Rank 2 of X, Rank 2 of XI...
            async exportMasterCSV() {
                let listX = this.drawnWinnersX;
                let listXI = this.drawnWinnersXI;

                // If a class has not been spun on stage yet, fetch active items for that class and shuffle them
                if (listX.length === 0) {
                    try {
                        const res = await fetch(`/api/categories/${this.activeSlug}/items?class_level=Kelas%20X`);
                        const data = await res.json();
                        if (data.success && data.items) {
                            let items = [...data.items];
                            for (let i = items.length - 1; i > 0; i--) {
                                const j = Math.floor(Math.random() * (i + 1));
                                [items[i], items[j]] = [items[j], items[i]];
                            }
                            listX = items.map(i => ({ item_title: i.title, subtitle: i.subtitle || 'Kelas X', class_level: 'Kelas X' }));
                        }
                    } catch(e) {}
                }

                if (listXI.length === 0) {
                    try {
                        const res = await fetch(`/api/categories/${this.activeSlug}/items?class_level=Kelas%20XI`);
                        const data = await res.json();
                        if (data.success && data.items) {
                            let items = [...data.items];
                            for (let i = items.length - 1; i > 0; i--) {
                                const j = Math.floor(Math.random() * (i + 1));
                                [items[i], items[j]] = [items[j], items[i]];
                            }
                            listXI = items.map(i => ({ item_title: i.title, subtitle: i.subtitle || 'Kelas XI', class_level: 'Kelas XI' }));
                        }
                    } catch(e) {}
                }

                // Master Interleaving: Rank 1 X, Rank 1 XI, Rank 2 X, Rank 2 XI...
                const masterCombined = [];
                const maxLen = Math.max(listX.length, listXI.length);
                for (let i = 0; i < maxLen; i++) {
                    if (listX[i]) {
                        masterCombined.push(listX[i]);
                    }
                    if (listXI[i]) {
                        masterCombined.push(listXI[i]);
                    }
                }

                if (masterCombined.length === 0) {
                    alert('Tidak ada data peserta untuk diekspor!');
                    return;
                }

                let csvContent = "\uFEFFNo Pentas,Nomor Giliran,Nama Peserta,Kelas & Kode Jurusan,Tingkat Kelas,Mata Lomba,Waktu Undi\n";
                masterCombined.forEach((item, idx) => {
                    const noPentas = `PENTAS #${String(idx + 1).padStart(2, '0')}`;
                    const classMajorTag = item.subtitle ? item.subtitle : item.class_level;
                    csvContent += `${idx + 1},${noPentas},"${item.item_title}","${classMajorTag}","${item.class_level}","${this.categoryName}","${item.drawn_at || ''}"\n`;
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.setAttribute("href", url);
                link.setAttribute("download", `master_urutan_pentas_${this.activeSlug}_acak_X_dan_XI_${Date.now()}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

            printPDF() {
                window.open(`/export/print-pdf?category=${encodeURIComponent(this.activeSlug)}`, '_blank');
            },

            playTickSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(600, ctx.currentTime);
                    gain.gain.setValueAtTime(0.08, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.04);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.04);
                } catch(e) {}
            },

            playChimeSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    [523.25, 659.25, 783.99, 1046.50].forEach((freq, idx) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, ctx.currentTime + idx * 0.08);
                        gain.gain.setValueAtTime(0.15, ctx.currentTime + idx * 0.08);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + idx * 0.08 + 0.4);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start(ctx.currentTime + idx * 0.08);
                        osc.stop(ctx.currentTime + idx * 0.08 + 0.4);
                    });
                } catch(e) {}
            }
        };
    }
</script>
@endpush
