<?php

namespace App\Livewire;

use Livewire\Component;

class LandingPage extends Component
{
    public string $activeFilter = 'all';

    public array $filterOptions = [
        'all'       => 'Semua Produk',
        'popular'   => 'Paling Populer',
        'new'       => 'Terbaru',
        'recommended' => 'Rekomendasi',
    ];

    private array $dummyProducts = [
        [
            'id'          => 'dummy-1',
            'cake_name'   => 'The Classic Nastar',
            'description' => 'Signature pineapple fill',
            'price'       => 85000,
            'image_url'   => '/images/placeholder-nastar.jpg',
            'badge'       => 'BESTSELLER',
            'badge_color' => '#D4EF70',
            'filter_tag'  => 'popular',
        ],
        [
            'id'          => 'dummy-2',
            'cake_name'   => 'Savory Kastangel',
            'description' => 'Triple cheese blend',
            'price'       => 92000,
            'image_url'   => '/images/placeholder-kastangel.jpg',
            'badge'       => 'PREMIUM',
            'badge_color' => '#D4EF70',
            'filter_tag'  => 'recommended',
        ],
        [
            'id'          => 'dummy-3',
            'cake_name'   => 'Dark Choco Sea Salt',
            'description' => '70% Cocoa organic',
            'price'       => 78000,
            'image_url'   => '/images/placeholder-choco.jpg',
            'badge'       => 'NEW',
            'badge_color' => '#D4EF70',
            'filter_tag'  => 'new',
        ],
        [
            'id'          => 'dummy-4',
            'cake_name'   => 'Matcha Zen White',
            'description' => 'Ceremonial grade tea',
            'price'       => 88000,
            'image_url'   => '/images/placeholder-matcha.jpg',
            'badge'       => 'SEASONAL',
            'badge_color' => '#D4EF70',
            'filter_tag'  => 'popular',
        ],
    ];

    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        sleep(0.7); // Artificial delay to show loading state
    }

    public function getFilteredProductsProperty(): array
    {
        // TODO Sprint Final: replace with FetchActiveProductsAction::execute($this->activeFilter)
        // TODO Sprint Final: pass $activeFilter to action for server-side filtering
        // TODO Sprint Final: filter 'popular'/'new'/'recommended' needs products.badge column or tag system
        return collect($this->dummyProducts)
            ->when(
                $this->activeFilter !== 'all',
                fn($c) =>
                $c->where('filter_tag', $this->activeFilter)
            )
            ->values()
            ->toArray();
    }

    public function addToCart(string $productId): void
    {
        // TODO SCRUM-14: enable addToCart() when UpdateCartAction is ready
        // TODO SCRUM-14: implement real cart logic
        // For now: dispatch browser event to show toast notification
        $this->dispatch('cart-unavailable', message: 'Cart feature coming soon!');
    }

    public function render()
    {
        return view('livewire.landing-page');
    }
}
