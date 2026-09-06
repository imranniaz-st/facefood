<?php

if (! defined('ABSPATH')) {
    exit;
}

class Facefood_Menu_Data
{
    /**
     * Facefood menu 2026 — sourced from Menu Facefood 2026 PDF.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function products(): array
    {
        return [
            ['sku' => 'FF-SW-001', 'category' => 'shawarma', 'name' => 'Chicken Shawarma', 'price' => 300],
            ['sku' => 'FF-SW-002', 'category' => 'shawarma', 'name' => 'Zinger Shawarma', 'price' => 400],
            ['sku' => 'FF-SW-003', 'category' => 'shawarma', 'name' => 'Loaded Shawarma', 'price' => 550],
            ['sku' => 'FF-AF-001', 'category' => 'afghani', 'name' => 'Simple Afghani', 'price' => 300],
            ['sku' => 'FF-AF-002', 'category' => 'afghani', 'name' => 'Chicken Afghani', 'price' => 500],
            ['sku' => 'FF-AF-003', 'category' => 'afghani', 'name' => 'Sausage Afghani', 'price' => 500],
            ['sku' => 'FF-AF-004', 'category' => 'afghani', 'name' => 'Zinger Afghani', 'price' => 500],
            ['sku' => 'FF-AF-005', 'category' => 'afghani', 'name' => 'Special Afghani', 'price' => 700],
            ['sku' => 'FF-BG-001', 'category' => 'burgers', 'name' => 'Zinger Burger', 'price' => 550],
            ['sku' => 'FF-BG-002', 'category' => 'burgers', 'name' => 'Patty Burger', 'price' => 450],
            ['sku' => 'FF-BG-003', 'category' => 'burgers', 'name' => 'Chicken Bite Burger', 'price' => 450],
            ['sku' => 'FF-BG-004', 'category' => 'burgers', 'name' => 'Fishinger Burger', 'price' => 750],
            ['sku' => 'FF-RP-001', 'category' => 'roll-paratha', 'name' => 'Chicken Roll', 'price' => 400],
            ['sku' => 'FF-RP-002', 'category' => 'roll-paratha', 'name' => 'Zinger Roll', 'price' => 450],
            ['sku' => 'FF-RP-003', 'category' => 'roll-paratha', 'name' => 'Special Roll', 'price' => 600],
            ['sku' => 'FF-PL-001', 'category' => 'platters', 'name' => 'Shawarma Platter', 'price' => 1000],
            ['sku' => 'FF-PL-002', 'category' => 'platters', 'name' => 'Zinger Platter', 'price' => 1100],
            ['sku' => 'FF-PL-003', 'category' => 'platters', 'name' => 'Special Platter', 'price' => 1650],
            ['sku' => 'FF-WR-001', 'category' => 'wraps', 'name' => 'Chicken Wrap', 'price' => 800],
            ['sku' => 'FF-WR-002', 'category' => 'wraps', 'name' => 'Broast Wrap', 'price' => 900],
            ['sku' => 'FF-FR-001', 'category' => 'fries', 'name' => 'Crispy Fries', 'price' => 250],
            ['sku' => 'FF-FR-002', 'category' => 'fries', 'name' => 'Garlic Mayo Fries', 'price' => 400],
            ['sku' => 'FF-FR-003', 'category' => 'fries', 'name' => 'Pizza Fries', 'price' => 650],
            ['sku' => 'FF-FR-004', 'category' => 'fries', 'name' => 'Loaded Fries', 'price' => 800],
            ['sku' => 'FF-CR-001', 'category' => 'crispy', 'name' => 'Broast Piece + Fries', 'price' => 600],
            ['sku' => 'FF-CR-002', 'category' => 'crispy', 'name' => 'Crunchy Strips + Fries', 'price' => 650],
            ['sku' => 'FF-CR-003', 'category' => 'crispy', 'name' => 'Nuggets + Fries', 'price' => 450],
            ['sku' => 'FF-KM-001', 'category' => 'kids-meal', 'name' => 'Mini Burger', 'price' => 300],
            ['sku' => 'FF-KM-002', 'category' => 'kids-meal', 'name' => 'Nuggets (3 Pcs) + Fries', 'price' => 300],
            ['sku' => 'FF-OD-001', 'category' => 'on-demand', 'name' => 'Pizza Shawarma', 'price' => 650],
            ['sku' => 'FF-OD-002', 'category' => 'on-demand', 'name' => 'Shawarma Grilled Chicken', 'price' => 950],
            ['sku' => 'FF-EX-001', 'category' => 'extras', 'name' => 'Extra Topping', 'price' => 150],
            ['sku' => 'FF-EX-002', 'category' => 'extras', 'name' => 'Chicken Items W/O Salad', 'price' => 50],
            ['sku' => 'FF-EX-003', 'category' => 'extras', 'name' => 'Extra Dip of Sauce', 'price' => 50],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function categories(): array
    {
        return [
            ['slug' => 'shawarma', 'name' => 'Shawarma'],
            ['slug' => 'afghani', 'name' => 'Afghani'],
            ['slug' => 'burgers', 'name' => 'Burgers'],
            ['slug' => 'roll-paratha', 'name' => 'Roll Paratha'],
            ['slug' => 'platters', 'name' => 'Platters'],
            ['slug' => 'wraps', 'name' => 'Wraps'],
            ['slug' => 'fries', 'name' => 'Fries'],
            ['slug' => 'crispy', 'name' => 'Crispy'],
            ['slug' => 'kids-meal', 'name' => 'Kids Meal'],
            ['slug' => 'on-demand', 'name' => 'On Demand'],
            ['slug' => 'extras', 'name' => 'Extras'],
        ];
    }
}
