<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\WheelItem;
use Illuminate\Database\Seeder;

class SpinwheelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. App Engine Default Settings
        $defaultSettings = [
            'spin_duration' => 5,
            'min_rotations' => 6,
            'easing_type' => 'cubic-ease-out',
            'auto_remove_winner' => false,
            'event_title' => 'Sanggar Seni Harisma',
            'sub_title' => 'Festival Cipta Seni & Budaya 2026 • SMKN 1 Ciamis',
            'batik_pattern_opacity' => 0.15,
            'sound_enabled' => true,
        ];

        foreach ($defaultSettings as $key => $val) {
            AppSetting::set($key, $val);
        }

        // Palette Colors for Wheel Segments (Diverse Ethnic Palette)
        $ethnicColors = [
            ['color' => '#8C2D19', 'text' => '#FFFFFF'], // Terracotta Red
            ['color' => '#255FA6', 'text' => '#FFFFFF'], // Royal Blue
            ['color' => '#B87314', 'text' => '#FFFFFF'], // Deep Gold/Bronze
            ['color' => '#6B21A8', 'text' => '#FFFFFF'], // Royal Purple
            ['color' => '#15803D', 'text' => '#FFFFFF'], // Emerald Green
            ['color' => '#C2410C', 'text' => '#FFFFFF'], // Burnt Orange
            ['color' => '#0F766E', 'text' => '#FFFFFF'], // Deep Teal
            ['color' => '#A16207', 'text' => '#FFFFFF'], // Antique Gold
        ];

        $majorsX = [
            'X AKL 1', 'X AKL 2', 'X PM 1', 'X PM 2', 'X KLN 1', 'X KLN 2',
            'X HTL 1', 'X HTL 2', 'X MPLB 1', 'X MPLB 2', 'X DKV 1', 'X DKV 2',
            'X PPLG 1', 'X PPLG 2', 'X AKL 3', 'X PM 3', 'X KLN 3', 'X HTL 3',
        ];

        $majorsXI = [
            'XI AKL 1', 'XI AKL 2', 'XI PM 1', 'XI PM 2', 'XI KLN 1', 'XI KLN 2',
            'XI HTL 1', 'XI HTL 2', 'XI MPLB 1', 'XI MPLB 2', 'XI DKV 1', 'XI DKV 2',
            'XI PPLG 1', 'XI PPLG 2', 'XI AKL 3', 'XI PM 3', 'XI KLN 3', 'XI HTL 3',
        ];

        // 2. Category 1: Solo Vocal
        $vocalCat = Category::updateOrCreate(
            ['slug' => 'solo-vocal'],
            [
                'name' => 'Solo Vocal',
                'description' => 'Penentuan nomor urut giliran pentas kelas peserta solo vocal secara acak, transparan, dan adil.',
                'icon' => 'mic',
                'is_active' => true,
            ]
        );

        // Delete existing items for clean seeding of class names
        WheelItem::where('category_id', $vocalCat->id)->delete();

        foreach ($majorsX as $idx => $className) {
            $palette = $ethnicColors[$idx % count($ethnicColors)];
            WheelItem::create([
                'category_id' => $vocalCat->id,
                'title' => $className,
                'subtitle' => 'SMKN 1 Ciamis',
                'class_level' => 'Kelas X',
                'color' => $palette['color'],
                'text_color' => $palette['text'],
                'weight' => 1,
                'is_active' => true,
            ]);
        }

        foreach ($majorsXI as $idx => $className) {
            $palette = $ethnicColors[($idx + 3) % count($ethnicColors)];
            WheelItem::create([
                'category_id' => $vocalCat->id,
                'title' => $className,
                'subtitle' => 'SMKN 1 Ciamis',
                'class_level' => 'Kelas XI',
                'color' => $palette['color'],
                'text_color' => $palette['text'],
                'weight' => 1,
                'is_active' => true,
            ]);
        }

        // 3. Category 2: Tari Modern
        $danceCat = Category::updateOrCreate(
            ['slug' => 'tari-modern'],
            [
                'name' => 'Tari Modern',
                'description' => 'Penentuan nomor urut giliran pentas kelas peserta tari modern secara acak, transparan, dan adil.',
                'icon' => 'sparkles',
                'is_active' => true,
            ]
        );

        WheelItem::where('category_id', $danceCat->id)->delete();

        foreach ($majorsX as $idx => $className) {
            $palette = $ethnicColors[($idx + 2) % count($ethnicColors)];
            WheelItem::create([
                'category_id' => $danceCat->id,
                'title' => $className,
                'subtitle' => 'SMKN 1 Ciamis',
                'class_level' => 'Kelas X',
                'color' => $palette['color'],
                'text_color' => $palette['text'],
                'weight' => 1,
                'is_active' => true,
            ]);
        }

        foreach ($majorsXI as $idx => $className) {
            $palette = $ethnicColors[($idx + 4) % count($ethnicColors)];
            WheelItem::create([
                'category_id' => $danceCat->id,
                'title' => $className,
                'subtitle' => 'SMKN 1 Ciamis',
                'class_level' => 'Kelas XI',
                'color' => $palette['color'],
                'text_color' => $palette['text'],
                'weight' => 1,
                'is_active' => true,
            ]);
        }

        // 4. Category 3: Sketching
        $sketchCat = Category::updateOrCreate(
            ['slug' => 'sketching'],
            [
                'name' => 'Sketching',
                'description' => 'Penentuan nomor urut giliran pentas kelas peserta sketching secara acak, transparan, dan adil.',
                'icon' => 'palette',
                'is_active' => true,
            ]
        );

        WheelItem::where('category_id', $sketchCat->id)->delete();

        foreach ($majorsX as $idx => $className) {
            $palette = $ethnicColors[($idx + 1) % count($ethnicColors)];
            WheelItem::create([
                'category_id' => $sketchCat->id,
                'title' => $className,
                'subtitle' => 'SMKN 1 Ciamis',
                'class_level' => 'Kelas X',
                'color' => $palette['color'],
                'text_color' => $palette['text'],
                'weight' => 1,
                'is_active' => true,
            ]);
        }

        foreach ($majorsXI as $idx => $className) {
            $palette = $ethnicColors[($idx + 5) % count($ethnicColors)];
            WheelItem::create([
                'category_id' => $sketchCat->id,
                'title' => $className,
                'subtitle' => 'SMKN 1 Ciamis',
                'class_level' => 'Kelas XI',
                'color' => $palette['color'],
                'text_color' => $palette['text'],
                'weight' => 1,
                'is_active' => true,
            ]);
        }
    }
}
