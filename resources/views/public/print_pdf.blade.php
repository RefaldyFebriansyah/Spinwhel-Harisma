<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan Master Urutan Pentas Peserta - SMKN 1 Ciamis</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FFF; color: #000; }
        h1, h2, h3, h4 { font-family: 'Times New Roman', Times, serif; }
        
        @media print {
            @page { margin: 12mm 15mm 15mm 15mm; size: A4 portrait; }
            .no-print { display: none !important; }
            body { background: #FFF !important; color: #000 !important; }
            .page-break { page-break-before: always; break-before: page; }
            .avoid-break { page-break-inside: avoid; break-inside: avoid; }
        }

        .double-border {
            border-bottom: 4px double #000;
        }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto" onload="window.print()">

    <!-- Print Control Floating Bar (Hidden when printing) -->
    <div class="no-print fixed top-4 right-4 flex items-center space-x-3 bg-zinc-900 text-white p-3 rounded-xl shadow-2xl border border-zinc-700 z-50">
        <button onclick="window.print()" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-bold text-xs rounded-lg transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak PDF / Print Semua Mata Lomba</span>
        </button>
        <button onclick="window.close()" class="px-3 py-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-semibold text-xs rounded-lg transition">
            Tutup
        </button>
    </div>

    <!-- Official Kop Surat / Formal Letterhead -->
    <div class="flex items-center justify-between pb-3 mb-4 double-border">
        <div class="w-20 flex justify-center">
            <img src="{{ asset('images/logo-harisma.png') }}" alt="Logo Harisma" class="w-16 h-16 object-contain">
        </div>
        <div class="text-center flex-1 px-4">
            <h3 class="text-sm font-bold uppercase tracking-wide text-black">
                DINAS PENDIDIKAN PROVINSI JAWA BARAT
            </h3>
            <h2 class="text-base font-black uppercase tracking-wider text-black">
                SMK NEGERI 1 CIAMIS &bull; SANGGAR SENI HARISMA
            </h2>
            <p class="text-[11px] text-zinc-800 font-serif leading-tight">
                Jl. Jendral Sudirman No. 269 Telp./Fax (0265) 771204 Ciamis 46215<br>
                Website: <span class="underline">www.smkn1ciamis.sch.id</span> &bull; Email: <span class="underline">sanggarseni@smkn1ciamis.sch.id</span>
            </p>
        </div>
        <div class="w-20 flex justify-center">
            <div class="w-14 h-14 rounded-full border-2 border-black flex items-center justify-center font-bold text-[10px] text-center p-1 uppercase">
                SMKN 1 CIAMIS
            </div>
        </div>
    </div>

    <!-- Formal Letter Heading -->
    <div class="text-center my-6">
        <h2 class="text-sm font-bold uppercase tracking-wide underline font-serif">
            SURAT KEPUTUSAN PANITIA FESTIVAL CIPTA SENI & BUDAYA 2026
        </h2>
        <p class="text-xs font-mono font-bold text-zinc-800 mt-0.5">
            Nomor: 042 / PAN-HARISMA / SMKN1 / IX / 2026
        </p>
        <p class="text-xs font-bold text-black mt-2 uppercase font-serif">
            TENTANG:<br>
            PENETAPAN MASTER URUTAN GILIRAN PENTAS PESERTA SELURUH MATA LOMBA
        </p>
    </div>

    <!-- Preamble / Opening Text -->
    <div class="text-xs text-justify leading-relaxed mb-6 space-y-2">
        <p>
            <strong>Menimbang:</strong> Bahwa untuk menjaga ketertiban, keadilan, dan kelancaran pelaksanaan Festival Cipta Seni & Budaya Sanggar Seni Harisma SMK Negeri 1 Ciamis tahun 2026, telah dilaksanakan pengundian nomor giliran pentas peserta untuk seluruh cabang mata lomba secara acak transparan.
        </p>
        <p>
            <strong>MEMUTUSKAN DAN MENETAPKAN:</strong> Master urutan giliran pentas peserta untuk seluruh cabang mata lomba yang disusun berselang-seling dari hasil pengundian <strong>Kelas X (urutan ke-1)</strong>, <strong>Kelas XI (urutan ke-1)</strong>, <strong>Kelas X (urutan ke-2)</strong>, <strong>Kelas XI (urutan ke-2)</strong>, dan seterusnya sebagai berikut:
        </p>
    </div>

    <!-- Loop through each Mata Lomba -->
    @foreach($masterCategoriesData as $index => $data)
        @php
            $cat = $data['category'];
            $items = $data['items'];
            $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];
            $roman = $romanNumerals[$index % count($romanNumerals)];
        @endphp

        <div class="{{ $index > 0 ? 'mt-8' : '' }} avoid-break">
            <!-- Sub-Heading Per Mata Lomba -->
            <div class="flex items-center justify-between border-b-2 border-black pb-1 mb-3">
                <h3 class="text-xs font-bold font-serif uppercase tracking-wider text-black">
                    {{ $roman }}. CABANG MATA LOMBA: <span class="underline">{{ strtoupper($cat->name) }}</span>
                </h3>
                <span class="text-[10px] font-mono font-bold text-zinc-700">
                    TOTAL: {{ count($items) }} PESERTA / PERWAKILAN
                </span>
            </div>

            <!-- Table Per Mata Lomba -->
            <table class="w-full text-left border-collapse border border-black text-xs mb-4">
                <thead>
                    <tr class="bg-zinc-100 text-black uppercase text-[10px] font-bold border-b border-black">
                        <th class="py-2 px-2 border border-black text-center w-10">No.</th>
                        <th class="py-2 px-2 border border-black text-center w-28">Kode Pentas</th>
                        <th class="py-2 px-3 border border-black">Kelas & Jurusan (Peserta Pentas)</th>
                        <th class="py-2 px-2 border border-black text-center w-24">Tingkat Kelas</th>
                        <th class="py-2 px-2 border border-black text-center w-28">Sekolah</th>
                        <th class="py-2 px-2 border border-black text-center w-24">Paraf Juri</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black">
                    @forelse($items as $idx => $item)
                        <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-zinc-50' }}">
                            <td class="py-1.5 px-2 border border-black text-center font-bold">{{ $idx + 1 }}</td>
                            <td class="py-1.5 px-2 border border-black text-center font-mono font-bold">PENTAS #{{ sprintf('%02d', $idx + 1) }}</td>
                            <td class="py-1.5 px-3 border border-black font-extrabold text-black">{{ $item->title }}</td>
                            <td class="py-1.5 px-2 border border-black text-center font-bold text-zinc-900">{{ $item->class_level }}</td>
                            <td class="py-1.5 px-2 border border-black text-center text-zinc-800">SMKN 1 Ciamis</td>
                            <td class="py-1.5 px-2 border border-black text-center"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-zinc-500 italic border border-black">Belum ada data peserta untuk mata lomba ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Formal Closing & Signatures Section -->
    <div class="mt-8 avoid-break">
        <p class="text-xs text-justify leading-relaxed mb-6">
            Demikian Surat Keputusan Penetapan Master Urutan Giliran Pentas ini diterbitkan untuk dipergunakan sebagaimana mestinya oleh seluruh peserta, guru pembina, dan dewan juri penilai.
        </p>

        <!-- Official 3-Column Signature Block -->
        <div class="text-xs">
            <div class="text-right mb-4">
                <p class="font-semibold">Ciamis, {{ now()->translatedFormat('d F Y') }}</p>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center">
                <!-- Signature 1 -->
                <div>
                    <p class="font-semibold text-zinc-700">Mengetahui,</p>
                    <p class="font-bold text-black">Ketua Panitia Festival</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-black underline">( ............................................ )</p>
                    <p class="text-[10px] text-zinc-600">NIS. ........................................</p>
                </div>

                <!-- Signature 2 -->
                <div>
                    <p class="font-semibold text-zinc-700">Menyetujui,</p>
                    <p class="font-bold text-black">Pembina Sanggar Seni Harisma</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-black underline">( ............................................ )</p>
                    <p class="text-[10px] text-zinc-600">NIP. ........................................</p>
                </div>

                <!-- Signature 3 -->
                <div>
                    <p class="font-semibold text-zinc-700">Mengesahkan,</p>
                    <p class="font-bold text-black">Kepala SMKN 1 Ciamis</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-black underline">( ............................................ )</p>
                    <p class="text-[10px] text-zinc-600">NIP. ........................................</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
