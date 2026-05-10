<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Collection;

class FetchActiveProductsAction
{
    /**
     * Return dummy active products for UI development.
     * TODO: replace with real Supabase query later.
     */
    public function __invoke(): Collection
    {
        return collect([
            (object) [
                'id'           => 1,
                'cake_name'    => 'The Structural Oat',
                'price'        => 45000,
                'weight_grams' => 120,
                'description'  => 'Dibangun di atas fondasi rolled oats panggang dan brown butter, diperkuat dengan strata dark chocolate, dan diselesaikan dengan taburan garam laut Maldon. Tidak akan hancur di bawah tekanan.',
                'image_url'    => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 2,
                'cake_name'    => 'Greenhouse Brownie',
                'price'        => 38000,
                'weight_grams' => 150,
                'description'  => 'Brownie fudgy dengan 70% dark chocolate Belgia, dihiasi walnut panggang dan sedikit flaky sea salt untuk kontras rasa yang sempurna.',
                'image_url'    => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 3,
                'cake_name'    => 'Botanical Croissant',
                'price'        => 32000,
                'weight_grams' => 90,
                'description'  => 'Croissant berlapis-lapis dengan 72 lipatan mentega Prancis premium. Renyah di luar, lembut dan airy di dalam.',
                'image_url'    => 'https://images.unsplash.com/photo-1555507036-ab1f4038024a?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 4,
                'cake_name'    => 'Forest Matcha Cake',
                'price'        => 55000,
                'weight_grams' => 200,
                'description'  => 'Kue matcha ceremonial-grade dari Uji, Jepang, dengan layer white chocolate ganache dan taburan kinako. Sebuah studi keseimbangan rasa.',
                'image_url'    => 'https://images.unsplash.com/photo-1571115177098-24ec42ed204d?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 5,
                'cake_name'    => 'Concrete Tiramisu',
                'price'        => 48000,
                'weight_grams' => 180,
                'description'  => 'Tiramisu dengan espresso single-origin, mascarpone Italia, dan dusting cocoa Valrhona. Solid seperti arsitektur, lembut seperti awan.',
                'image_url'    => 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 6,
                'cake_name'    => 'Raw Honey Madeleine',
                'price'        => 28000,
                'weight_grams' => 60,
                'description'  => 'Madeleine klasik dengan raw honey dari peternakan lebah lokal. Tekstur shell yang sempurna dengan aroma citrus lembut.',
                'image_url'    => 'https://images.unsplash.com/photo-1508737027454-e6454ef45afd?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 7,
                'cake_name'    => 'Brutalist Banana Bread',
                'price'        => 42000,
                'weight_grams' => 250,
                'description'  => 'Banana bread dengan pisang cavendish matang sempurna, brown butter, dan crunchy walnut topping. Comfort food yang tidak minta maaf.',
                'image_url'    => 'https://images.unsplash.com/photo-1605090930601-b2e1adfa5284?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
            (object) [
                'id'           => 8,
                'cake_name'    => 'Tectonic Cinnamon Roll',
                'price'        => 35000,
                'weight_grams' => 140,
                'description'  => 'Cinnamon roll dengan lapisan kayu manis Ceylon dan cream cheese frosting tebal. Setiap gigitan adalah peristiwa geologis.',
                'image_url'    => 'https://images.unsplash.com/photo-1509365390695-33aee754301f?w=600&h=600&fit=crop',
                'is_active'    => true,
            ],
        ]);
    }
}
