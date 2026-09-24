<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\SpinHistory;
use App\Models\WheelItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSpinwheelController extends Controller
{
    /**
     * Render the Stage / Projector ready Spinwheel view.
     */
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)->with('activeWheelItems')->get();
        $selectedSlug = $request->query('category', $categories->first()?->slug ?? 'solo-vocal');
        $activeCategory = $categories->firstWhere('slug', $selectedSlug) ?? $categories->first();
        $selectedClass = $request->query('class_level', 'Semua Kelas');

        $engineConfig = AppSetting::getEngineConfig();

        $histories = SpinHistory::with('category')
            ->where('category_id', $activeCategory?->id)
            ->latest('spun_at')
            ->take(30)
            ->get();

        return view('public.stage', compact('categories', 'activeCategory', 'selectedClass', 'engineConfig', 'histories'));
    }

    /**
     * Fetch items for a category formatted for Canvas rendering, with optional class level filter.
     */
    public function getItems(Request $request, string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $classLevel = $request->query('class_level', 'Semua Kelas');

        $query = WheelItem::where('category_id', $category->id)->where('is_active', true);

        if ($classLevel !== 'Semua Kelas' && ! empty($classLevel)) {
            $cleanClass = trim($classLevel);
            $query->where(function ($q) use ($cleanClass) {
                $q->where('class_level', $cleanClass)
                    ->orWhere('class_level', 'LIKE', '%'.$cleanClass.'%');
            });
        }

        $items = $query->orderBy('id', 'asc')
            ->get(['id', 'title', 'subtitle', 'class_level', 'color', 'text_color', 'weight', 'times_won']);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'class_level' => $classLevel,
            'items' => $items,
        ]);
    }

    /**
     * Record winning spin result.
     */
    public function recordWinner(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'wheel_item_id' => 'nullable|exists:wheel_items,id',
            'item_title' => 'required|string',
            'class_level' => 'nullable|string',
            'executor' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = null;
        if (! empty($validated['wheel_item_id'])) {
            $item = WheelItem::find($validated['wheel_item_id']);
        }

        if ($item) {
            $item->increment('times_won');
        }

        $autoRemove = AppSetting::get('auto_remove_winner', false);
        $shouldRemove = $request->boolean('auto_remove', $autoRemove);

        if ($item && $shouldRemove) {
            $item->update(['is_active' => false]);
        }

        $classLevel = $validated['class_level'] ?? ($item?->class_level ?? 'Semua Kelas');

        $history = SpinHistory::create([
            'category_id' => $validated['category_id'],
            'wheel_item_id' => $item?->id,
            'item_title' => $validated['item_title'],
            'class_level' => $classLevel,
            'executor_name' => $validated['executor'] ?? 'Panitia / Admin',
            'notes' => $validated['notes'] ?? null,
            'spun_at' => now(),
        ]);

        $query = WheelItem::where('category_id', $validated['category_id'])->where('is_active', true);
        if ($classLevel !== 'Semua Kelas') {
            $query->where('class_level', $classLevel);
        }
        $activeItems = $query->get(['id', 'title', 'subtitle', 'class_level', 'color', 'text_color', 'weight', 'times_won']);

        return response()->json([
            'success' => true,
            'message' => 'Hasil putaran berhasil dicatat!',
            'history' => $history->load('category'),
            'item_removed' => $item && $shouldRemove,
            'remaining_items' => $activeItems,
        ]);
    }

    /**
     * Quick toggle item active status from public modal.
     */
    public function toggleItem(WheelItem $item): JsonResponse
    {
        $item->update(['is_active' => ! $item->is_active]);

        $activeItems = WheelItem::where('category_id', $item->category_id)
            ->where('is_active', true)
            ->get(['id', 'title', 'subtitle', 'class_level', 'color', 'text_color', 'weight', 'times_won']);

        return response()->json([
            'success' => true,
            'message' => "Item '{$item->title}' ".($item->is_active ? 'diaktifkan kembali.' : 'dinonaktifkan dari roda.'),
            'is_active' => $item->is_active,
            'remaining_items' => $activeItems,
        ]);
    }

    /**
     * Render Printable Master PDF View for ALL Categories in one official document.
     */
    public function printMasterPdf(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('id', 'asc')->get();
        $masterCategoriesData = [];

        foreach ($categories as $category) {
            // Get latest spin history for Kelas X
            $historyX = SpinHistory::where('category_id', $category->id)
                ->where('class_level', 'Kelas X')
                ->latest('spun_at')
                ->first();

            // Get latest spin history for Kelas XI
            $historyXI = SpinHistory::where('category_id', $category->id)
                ->where('class_level', 'Kelas XI')
                ->latest('spun_at')
                ->first();

            $itemsX = [];
            if ($historyX && ! empty($historyX->notes)) {
                $parts = explode(' | ', $historyX->notes);
                foreach ($parts as $p) {
                    $clean = trim(preg_replace('/^Urutan \d+: /', '', $p));
                    if (empty($clean)) {
                        continue;
                    }

                    $lastParen = strrpos($clean, '(');
                    if ($lastParen !== false) {
                        $title = trim(substr($clean, 0, $lastParen));
                        $subtitle = trim(substr($clean, $lastParen + 1), '() ');
                    } else {
                        $title = $clean;
                        $subtitle = 'Kelas X';
                    }

                    $itemsX[] = (object) [
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'class_level' => 'Kelas X',
                    ];
                }
            }

            if (empty($itemsX)) {
                $itemsX = WheelItem::where('category_id', $category->id)
                    ->where('class_level', 'Kelas X')
                    ->where('is_active', true)
                    ->get();
            }

            $itemsXI = [];
            if ($historyXI && ! empty($historyXI->notes)) {
                $parts = explode(' | ', $historyXI->notes);
                foreach ($parts as $p) {
                    $clean = trim(preg_replace('/^Urutan \d+: /', '', $p));
                    if (empty($clean)) {
                        continue;
                    }

                    $lastParen = strrpos($clean, '(');
                    if ($lastParen !== false) {
                        $title = trim(substr($clean, 0, $lastParen));
                        $subtitle = trim(substr($clean, $lastParen + 1), '() ');
                    } else {
                        $title = $clean;
                        $subtitle = 'Kelas XI';
                    }

                    $itemsXI[] = (object) [
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'class_level' => 'Kelas XI',
                    ];
                }
            }

            if (empty($itemsXI)) {
                $itemsXI = WheelItem::where('category_id', $category->id)
                    ->where('class_level', 'Kelas XI')
                    ->where('is_active', true)
                    ->get();
            }

            // Interleave strictly: Kelas X first (#1), Kelas XI second (#1), Kelas X third (#2), Kelas XI fourth (#2)...
            $interleaved = [];
            $maxCount = max(count($itemsX), count($itemsXI));
            for ($i = 0; $i < $maxCount; $i++) {
                if (isset($itemsX[$i])) {
                    $interleaved[] = (object) $itemsX[$i];
                }
                if (isset($itemsXI[$i])) {
                    $interleaved[] = (object) $itemsXI[$i];
                }
            }

            $masterCategoriesData[] = [
                'category' => $category,
                'items' => $interleaved,
            ];
        }

        $engineConfig = AppSetting::getEngineConfig();

        return view('public.print_pdf', compact('masterCategoriesData', 'engineConfig'));
    }
}
