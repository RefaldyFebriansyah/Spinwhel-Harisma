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
    /**
     * Fetch items for a category formatted for Canvas rendering, with optional class level filter.
     */
    public function getItems(Request $request, string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $classLevel = $request->query('class_level', 'Semua Kelas');

        $query = WheelItem::where('category_id', $category->id)->where('is_active', true);

        if ($classLevel !== 'Semua Kelas' && ! empty($classLevel)) {
            $query->where('class_level', $classLevel);
        }

        $items = $query->orderBy('id', 'asc')
            ->get(['id', 'title', 'subtitle', 'class_level', 'color', 'text_color', 'weight', 'times_won']);

        $hasHistory = SpinHistory::where('category_id', $category->id)
            ->when($classLevel !== 'Semua Kelas' && ! empty($classLevel), fn ($q) => $q->where('class_level', $classLevel))
            ->exists();

        $drawnSequence = $hasHistory ? $this->getParsedSpinSequence($category->id, $classLevel) : [];

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'class_level' => $classLevel,
            'items' => $items,
            'has_history' => $hasHistory,
            'drawn_sequence' => $drawnSequence,
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
     * Helper to parse history items for a category and class level.
     */
    private function getParsedSpinSequence(int $categoryId, string $classLevel): array
    {
        $history = SpinHistory::where('category_id', $categoryId)
            ->where('class_level', $classLevel)
            ->latest('spun_at')
            ->first();

        $items = [];
        if ($history && ! empty($history->notes)) {
            $parts = explode(' | ', $history->notes);
            foreach ($parts as $p) {
                $clean = trim(preg_replace('/^Urutan( ke-)? \d+: /i', '', $p));
                if (empty($clean)) {
                    continue;
                }

                $lastParen = strrpos($clean, '(');
                if ($lastParen !== false) {
                    $title = trim(substr($clean, 0, $lastParen));
                    $subtitle = trim(substr($clean, $lastParen + 1), '() ');
                } else {
                    $title = $clean;
                    $subtitle = $classLevel;
                }

                $items[] = (object) [
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'class_level' => $classLevel,
                ];
            }
        }

        // If no notes-based history, try individual single spins
        if (empty($items)) {
            $individualHistories = SpinHistory::where('category_id', $categoryId)
                ->where('class_level', $classLevel)
                ->whereNotNull('wheel_item_id')
                ->orderBy('spun_at', 'asc')
                ->get();

            foreach ($individualHistories as $h) {
                $items[] = (object) [
                    'title' => $h->item_title,
                    'subtitle' => $h->class_level,
                    'class_level' => $h->class_level,
                ];
            }
        }

        $dbItems = WheelItem::where('category_id', $categoryId)
            ->where('class_level', $classLevel)
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        if (empty($items)) {
            // Deterministic order by ID if never spun on stage yet
            $items = $dbItems->map(fn ($r) => (object) [
                'title' => $r->title,
                'subtitle' => $r->subtitle ?: $classLevel,
                'class_level' => $classLevel,
            ])->all();
        } elseif (count($items) < count($dbItems)) {
            $existingTitles = array_map(fn ($i) => $i->title, $items);
            $remaining = $dbItems->reject(fn ($i) => in_array($i->title, $existingTitles));
            foreach ($remaining as $r) {
                $items[] = (object) [
                    'title' => $r->title,
                    'subtitle' => $r->subtitle ?: $classLevel,
                    'class_level' => $classLevel,
                ];
            }
        }

        return $items;
    }

    /**
     * Render Printable Master PDF View for ALL Categories in one official document.
     */
    public function printMasterPdf(Request $request): View
    {
        $categories = Category::where('is_active', true)->orderBy('id', 'asc')->get();
        $masterCategoriesData = [];

        foreach ($categories as $category) {
            $itemsX = $this->getParsedSpinSequence($category->id, 'Kelas X');
            $itemsXI = $this->getParsedSpinSequence($category->id, 'Kelas XI');

            // Interleave strictly: Kelas X (#1), Kelas XI (#1), Kelas X (#2), Kelas XI (#2)...
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
