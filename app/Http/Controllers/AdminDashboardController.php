<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\SpinHistory;
use App\Models\WheelItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Overview.
     */
    public function index(): View
    {
        $totalItems = WheelItem::count();
        $activeItems = WheelItem::where('is_active', true)->count();
        $itemsKelasX = WheelItem::where('class_level', 'Kelas X')->count();
        $itemsKelasXI = WheelItem::where('class_level', 'Kelas XI')->count();
        $spinsToday = SpinHistory::whereDate('spun_at', today())->count();
        $totalSpins = SpinHistory::count();

        $categories = Category::withCount(['wheelItems', 'activeWheelItems'])->get();

        $recentSpins = SpinHistory::with('category', 'wheelItem')
            ->latest('spun_at')
            ->take(10)
            ->get();

        $engineConfig = AppSetting::getEngineConfig();

        return view('admin.dashboard', compact(
            'totalItems',
            'activeItems',
            'itemsKelasX',
            'itemsKelasXI',
            'spinsToday',
            'totalSpins',
            'categories',
            'recentSpins',
            'engineConfig'
        ));
    }

    /**
     * Item Management Page.
     */
    public function items(Request $request): View
    {
        $categories = Category::all();
        $selectedCategoryId = $request->query('category_id', $categories->first()?->id);
        $selectedClass = $request->query('class_level');

        $query = WheelItem::with('category');
        if ($selectedCategoryId) {
            $query->where('category_id', $selectedCategoryId);
        }

        if ($selectedClass) {
            $query->where('class_level', $selectedClass);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        $items = $query->orderBy('id', 'desc')->paginate(15);
        $activeCategory = $categories->firstWhere('id', $selectedCategoryId);

        return view('admin.items', compact('items', 'categories', 'activeCategory', 'selectedClass'));
    }

    /**
     * Store new Wheel Item.
     */
    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'class_level' => 'required|string|in:Kelas X,Kelas XI',
            'color' => 'required|string|max:20',
            'text_color' => 'required|string|max:20',
            'weight' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        WheelItem::create($validated);

        return redirect()->back()->with('success', 'Peserta/Item berhasil ditambahkan!');
    }

    /**
     * Update existing Wheel Item.
     */
    public function updateItem(Request $request, WheelItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'class_level' => 'required|string|in:Kelas X,Kelas XI',
            'color' => 'required|string|max:20',
            'text_color' => 'required|string|max:20',
            'weight' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $item->update($validated);

        return redirect()->back()->with('success', 'Peserta/Item berhasil diperbarui!');
    }

    /**
     * Delete Wheel Item.
     */
    public function destroyItem(WheelItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()->back()->with('success', 'Peserta/Item berhasil dihapus!');
    }

    /**
     * Bulk Import items (Line by line text or CSV).
     */
    public function bulkImport(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'class_level' => 'required|string|in:Kelas X,Kelas XI',
            'bulk_text' => 'required_without:csv_file|nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt|max:2048',
        ]);

        $categoryId = $request->category_id;
        $classLevel = $request->class_level;
        $itemsToCreate = [];

        $presetColors = ['#8C2D19', '#255FA6', '#B87314', '#6B21A8', '#15803D', '#C2410C', '#0F766E', '#A16207'];

        if ($request->hasFile('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $file = fopen($path, 'r');
            $header = fgetcsv($file); // Check if header exists

            $colorIdx = 0;
            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0])) {
                    continue;
                }
                $title = trim($row[0]);
                $subtitle = isset($row[1]) ? trim($row[1]) : $classLevel;
                $rowClass = isset($row[2]) ? trim($row[2]) : $classLevel;
                $color = $presetColors[$colorIdx % count($presetColors)];
                $colorIdx++;

                $itemsToCreate[] = [
                    'category_id' => $categoryId,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'class_level' => $rowClass,
                    'color' => $color,
                    'text_color' => '#FFFFFF',
                    'weight' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            fclose($file);
        } elseif ($request->filled('bulk_text')) {
            $lines = explode("\n", $request->bulk_text);
            $colorIdx = 0;
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                $parts = explode(',', $line);
                $title = trim($parts[0]);
                $subtitle = isset($parts[1]) ? trim($parts[1]) : $classLevel;
                $color = $presetColors[$colorIdx % count($presetColors)];
                $colorIdx++;

                $itemsToCreate[] = [
                    'category_id' => $categoryId,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'class_level' => $classLevel,
                    'color' => $color,
                    'text_color' => '#FFFFFF',
                    'weight' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (count($itemsToCreate) > 0) {
            WheelItem::insert($itemsToCreate);

            return redirect()->back()->with('success', count($itemsToCreate)." peserta/item {$classLevel} berhasil di-import!");
        }

        return redirect()->back()->with('error', 'Tidak ada data valid yang dapat di-import.');
    }

    /**
     * Engine & Branding Settings Page.
     */
    public function settings(): View
    {
        $engineConfig = AppSetting::getEngineConfig();

        return view('admin.settings', compact('engineConfig'));
    }

    /**
     * Update Engine Settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'spin_duration' => 'required|numeric|min:2|max:30',
            'min_rotations' => 'required|integer|min:2|max:20',
            'easing_type' => 'required|string',
            'auto_remove_winner' => 'nullable',
            'event_title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'batik_pattern_opacity' => 'required|numeric|min:0|max:1',
            'sound_enabled' => 'nullable',
        ]);

        AppSetting::set('spin_duration', $validated['spin_duration']);
        AppSetting::set('min_rotations', $validated['min_rotations']);
        AppSetting::set('easing_type', $validated['easing_type']);
        AppSetting::set('auto_remove_winner', $request->has('auto_remove_winner'));
        AppSetting::set('event_title', $validated['event_title']);
        AppSetting::set('sub_title', $validated['sub_title']);
        AppSetting::set('batik_pattern_opacity', $validated['batik_pattern_opacity']);
        AppSetting::set('sound_enabled', $request->has('sound_enabled'));

        return redirect()->back()->with('success', 'Pengaturan mesin spinwheel & tampilan berhasil disimpan!');
    }

    /**
     * History Logs Page.
     */
    public function history(Request $request): View
    {
        $categories = Category::all();
        $query = SpinHistory::with('category', 'wheelItem');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('class_level')) {
            $query->where('class_level', $request->class_level);
        }

        if ($request->filled('search')) {
            $query->where('item_title', 'like', '%'.$request->search.'%');
        }

        $histories = $query->latest('spun_at')->paginate(20);

        return view('admin.history', compact('histories', 'categories'));
    }

    /**
     * Export History Logs to CSV file.
     */
    public function exportHistory(Request $request): Response
    {
        $query = SpinHistory::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('class_level')) {
            $query->where('class_level', $request->class_level);
        }

        $histories = $query->latest('spun_at')->get();

        $csvHeader = ['No', 'ID', 'Waktu Putar', 'Mata Lomba / Kategori', 'Kelas', 'Nama Peserta / Hasil Undian', 'Operator / Admin'];
        $csvRows = [];

        $idx = 1;
        foreach ($histories as $log) {
            $csvRows[] = [
                $idx++,
                $log->id,
                $log->spun_at ? $log->spun_at->format('Y-m-d H:i:s') : '-',
                $log->category?->name ?? 'Umum',
                $log->class_level ?? 'Kelas X',
                $log->item_title,
                $log->executor_name,
            ];
        }

        $handle = fopen('php://temp', 'r+');
        // UTF-8 BOM for Microsoft Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($handle, $csvHeader);
        foreach ($csvRows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        $filename = 'hasil_undian_peserta_harisma_'.date('Y-m-d_H-i-s').'.csv';

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Reset History log table.
     */
    public function resetHistory(): RedirectResponse
    {
        SpinHistory::query()->delete();

        return redirect()->back()->with('success', 'Seluruh riwayat putaran telah dibersihkan!');
    }
}
