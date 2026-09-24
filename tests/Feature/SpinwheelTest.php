<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\WheelItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpinwheelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_stage_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SANGGAR SENI HARISMA');
    }

    public function test_api_returns_category_items_filtered_by_class_level(): void
    {
        $category = Category::first();
        $response = $this->get(route('api.items', [
            'slug' => $category->slug,
            'class_level' => 'Kelas X',
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'category' => ['id', 'name', 'slug'],
            'class_level',
            'items',
        ]);

        $this->assertEquals('Kelas X', $response->json('class_level'));
        $this->assertGreaterThanOrEqual(18, count($response->json('items')));
    }

    public function test_record_winner_creates_spin_history_with_class_level(): void
    {
        $category = Category::first();
        $item = WheelItem::where('category_id', $category->id)->where('class_level', 'Kelas X')->first();

        $response = $this->postJson(route('api.spin.record'), [
            'category_id' => $category->id,
            'wheel_item_id' => $item->id,
            'item_title' => $item->title,
            'class_level' => 'Kelas X',
            'executor' => 'Tester',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('spin_histories', [
            'category_id' => $category->id,
            'wheel_item_id' => $item->id,
            'item_title' => $item->title,
            'class_level' => 'Kelas X',
            'executor_name' => 'Tester',
        ]);
    }

    public function test_toggle_item_active_status(): void
    {
        $item = WheelItem::first();
        $initialState = $item->is_active;

        $response = $this->postJson(route('api.items.toggle', $item));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_active' => ! $initialState]);
        $this->assertDatabaseHas('wheel_items', [
            'id' => $item->id,
            'is_active' => ! $initialState,
        ]);
    }

    public function test_admin_dashboard_loads(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
    }

    public function test_admin_can_create_wheel_item(): void
    {
        $category = Category::first();

        $response = $this->post(route('admin.items.store'), [
            'category_id' => $category->id,
            'title' => 'Peserta Baru',
            'subtitle' => 'Sub Judul',
            'class_level' => 'Kelas X',
            'color' => '#8C2D19',
            'text_color' => '#FFFFFF',
            'weight' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('wheel_items', [
            'category_id' => $category->id,
            'title' => 'Peserta Baru',
            'class_level' => 'Kelas X',
        ]);
    }

    public function test_admin_can_export_history(): void
    {
        $response = $this->get(route('admin.history.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
