<!DOCTYPE html>
<html lang="id" class="h-full bg-[#090A0F]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Spinwheel Sanggar Seni Harisma')</title>

    <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js & Lucide Icons -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, .font-serif-ethnic {
            font-family: 'Cinzel', serif;
        }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-batik-pattern text-zinc-100 antialiased flex flex-col justify-between overflow-x-hidden">

    @yield('content')

    <!-- Lucide Icon Initialization & Web Audio Synthesizer Engine -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Web Audio API Synthesizer for Wheel Tick SFX & Fanfare Sound Effects
        class SoundEngine {
            constructor() {
                this.ctx = null;
                this.enabled = true;
            }

            init() {
                if (!this.ctx) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    this.ctx = new AudioContext();
                }
                if (this.ctx.state === 'suspended') {
                    this.ctx.resume();
                }
            }

            // Click / Tick Sound when wheel pointer passes segment boundary
            playTick() {
                if (!this.enabled) return;
                this.init();

                try {
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(600, this.ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(120, this.ctx.currentTime + 0.04);

                    gain.gain.setValueAtTime(0.3, this.ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 0.04);

                    osc.connect(gain);
                    gain.connect(this.ctx.destination);

                    osc.start();
                    osc.stop(this.ctx.currentTime + 0.04);
                } catch (e) {
                    console.warn('Audio tick play error:', e);
                }
            }

            // Cheering Fanfare Victory Sound
            playFanfare() {
                if (!this.enabled) return;
                this.init();

                try {
                    const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6
                    notes.forEach((freq, idx) => {
                        const osc = this.ctx.createOscillator();
                        const gain = this.ctx.createGain();

                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(freq, this.ctx.currentTime + idx * 0.12);

                        gain.gain.setValueAtTime(0.3, this.ctx.currentTime + idx * 0.12);
                        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + idx * 0.12 + 0.5);

                        osc.connect(gain);
                        gain.connect(this.ctx.destination);

                        osc.start(this.ctx.currentTime + idx * 0.12);
                        osc.stop(this.ctx.currentTime + idx * 0.12 + 0.5);
                    });
                } catch (e) {
                    console.warn('Fanfare audio play error:', e);
                }
            }
        }

        window.soundEngine = new SoundEngine();
    </script>

    @stack('scripts')
</body>
</html>
