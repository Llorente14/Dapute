<?php

namespace App\Actions\Cart;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateCartAction
{
    /**
     * Menambahkan produk ke keranjang dengan mekanisme snapshot.
     */
    public function add(string $userId, string $productId, int $quantity = 1): array
    {
        try {
            // Gunakan whereRaw untuk memastikan PostgreSQL membaca boolean murni
            $product = DB::table('products')
                ->where('id', $productId)
                ->whereRaw('is_active = true') 
                ->first();

            if (!$product) {
                return ['success' => false, 'message' => 'Product not available or inactive.'];
            }

            // Gunakan UPSERT untuk menangani UNIQUE(user_id, product_id)
            // Jika ada, increment quantity. Jika tidak, insert baru dengan snapshot.
            $existing = DB::table('carts')
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                $newQty = min(99, $existing->quantity + $quantity);
                DB::table('carts')->where('id', $existing->id)->update([
                    'quantity' => $newQty,
                    'updated_at' => now()
                ]);
            } else {
                DB::table('carts')->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'cake_name_snapshot' => $product->cake_name,
                    'price_snapshot' => (int) $product->price,
                    'image_url_snapshot' => $product->image_url,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return [
                'success' => true, 
                'cart_count' => $this->getCount($userId)
            ];
        } catch (\Exception $e) {
            Log::error("CartAdd Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to add item to cart.'];
        }
    }

    public function increment(string $cartItemId, string $userId): array
    {
        $item = DB::table('carts')->where('id', $cartItemId)->where('user_id', $userId)->first();
        
        if (!$item) return ['success' => false, 'message' => 'Item not found.'];
        if ($item->quantity >= 99) return ['success' => false, 'message' => 'Maximum 99 items'];

        $newQty = $item->quantity + 1;
        DB::table('carts')->where('id', $cartItemId)->update(['quantity' => $newQty, 'updated_at' => now()]);

        return [
            'success' => true, 
            'new_quantity' => $newQty, 
            'cart_count' => $this->getCount($userId)
        ];
    }

    public function decrement(string $cartItemId, string $userId): array
    {
        $item = DB::table('carts')->where('id', $cartItemId)->where('user_id', $userId)->first();
        
        if (!$item) return ['success' => false, 'message' => 'Item not found.'];
        if ($item->quantity <= 1) return ['success' => false, 'message' => 'Minimum 1 item'];

        $newQty = $item->quantity - 1;
        DB::table('carts')->where('id', $cartItemId)->update(['quantity' => $newQty, 'updated_at' => now()]);

        return [
            'success' => true, 
            'new_quantity' => $newQty, 
            'cart_count' => $this->getCount($userId)
        ];
    }

    public function remove(string $cartItemId, string $userId): array
    {
        DB::table('carts')->where('id', $cartItemId)->where('user_id', $userId)->delete();
        return ['success' => true, 'cart_count' => $this->getCount($userId)];
    }

    public function getItems(string $userId): array
    {
        // Return kolom id sebagai cart_item_id
        return DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->where('carts.user_id', $userId)
            ->select('carts.id as cart_item_id', 'carts.product_id', 'carts.cake_name_snapshot', 'carts.price_snapshot', 'carts.image_url_snapshot', 'carts.quantity', 'products.weight_grams')
            ->get()
            ->map(function($item) {
                $item->price_snapshot = (int) $item->price_snapshot;
                $item->weight_grams = (int) $item->weight_grams;
                return (array) $item;
            })
            ->toArray();
    }

    public function getCount(string $userId): int
    {
        // SUM total quantity
        return (int) DB::table('carts')->where('user_id', $userId)->sum('quantity') ?: 0;
    }
}