<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\StoreProductAction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCrudForm extends Component
{
    use WithFileUploads;

    // ── Route parameter ────────────────────────────────────────────────────
    public ?string $productId = null;   // null = create mode, uuid = edit mode

    // ── Form fields ────────────────────────────────────────────────────────
    #[Validate('required|string|max:255')]
    public string $cake_name = '';

    #[Validate('nullable|string|max:2000')]
    public string $description = '';

    #[Validate('required|integer|min:0')]
    public int $price = 0;

    #[Validate('required|integer|min:1')]
    public int $weight_grams = 0;

    #[Validate('nullable|image|max:2048')]   // 2 MB
    public $photo = null;

    #[Validate('boolean')]
    public bool $is_active = true;

    // ── Internal state ─────────────────────────────────────────────────────
    public ?string $existingImageUrl = null;
    public bool    $isEditMode       = false;

    // ── Lifecycle ──────────────────────────────────────────────────────────
    public function mount(?string $productId = null): void
    {
        $this->productId = $productId;

        if ($productId) {
            $product = Product::findOrFail($productId);
            $this->fill([
                'cake_name'    => $product->cake_name,
                'description'  => $product->description ?? '',
                'price'        => $product->price,
                'weight_grams' => $product->weight_grams ?? 0,
                'is_active'    => $product->is_active,
            ]);
            $this->existingImageUrl = $product->image_url;
            $this->isEditMode       = true;
        }
    }

    // ── Save (create OR update) ────────────────────────────────────────────
    public function save(StoreProductAction $storeAction): void
    {
        // Cast string inputs from HTML form to correct types before validation
        $this->price        = (int) $this->price;
        $this->weight_grams = (int) $this->weight_grams;

        $this->validate();

        $isActive = (bool) $this->is_active ? 'true' : 'false';
        $data = [
            'cake_name'    => $this->cake_name,
            'description'  => $this->description ?: null,
            'price'        => $this->price,
            'weight_grams' => $this->weight_grams,
            'is_active'    => DB::raw("'{$isActive}'::boolean"),
        ];


        if ($this->isEditMode) {
            // ── UPDATE: handle image replacement via Supabase Storage
            $imageUrl = $this->existingImageUrl;

            if ($this->photo) {
                $supabaseUrl = env('SUPABASE_URL');
                $serviceKey  = env('SUPABASE_SERVICE_ROLE_KEY');

                $extension    = $this->photo->getClientOriginalExtension();
                // Add timestamp suffix so the path is unique and CDN cache is bypassed
                $baseName     = Str::slug(pathinfo($this->photo->getClientOriginalName(), PATHINFO_FILENAME));
                $safeFilename = $baseName . '-' . time() . '.' . $extension;
                $path         = "products/{$this->productId}/{$safeFilename}";

                $upload = Http::withHeaders([
                    'Authorization' => "Bearer {$serviceKey}",
                    'Content-Type'  => $this->photo->getMimeType(),
                    'x-upsert'      => 'true',   // allow overwrite existing file
                ])->withBody(file_get_contents($this->photo->getRealPath()), $this->photo->getMimeType())
                    ->post("{$supabaseUrl}/storage/v1/object/product-images/{$path}");

                if ($upload->successful()) {
                    $imageUrl = "{$supabaseUrl}/storage/v1/object/public/product-images/{$path}";
                } else {
                    Log::error('ProductCrudForm: image upload failed on update', [
                        'status'   => $upload->status(),
                        'response' => $upload->body(),
                    ]);
                    $this->addError('photo', 'Gagal mengunggah gambar, coba lagi.');
                    return;
                }
            }

            DB::table('products')
                ->where('id', $this->productId)
                ->update(array_merge($data, ['image_url' => $imageUrl]));

            session()->flash('success', 'Produk berhasil diperbarui.');
        } else {
            // ── CREATE: delegate to StoreProductAction (handles Supabase Storage upload)
            $uploadedFile = $this->photo
                ? $this->photo->getRealPath() !== false ? $this->photo : null
                : null;

            $result = $storeAction->execute($data, $uploadedFile);

            if (! $result['success']) {
                $this->addError('cake_name', $result['message'] ?? 'Gagal menyimpan produk.');
                return;
            }

            session()->flash('success', 'Produk berhasil ditambahkan.');
        }

        $this->redirect(route('admin.products.index'), navigate: true);
    }

    // ── Delete ─────────────────────────────────────────────────────────────
    public function delete(): void
    {
        if (! $this->isEditMode) {
            return;
        }

        DB::table('products')->where('id', $this->productId)->delete();

        session()->flash('success', 'Produk berhasil dihapus.');
        $this->redirect(route('admin.products.index'), navigate: true);
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    private function resetForm(): void
    {
        $this->reset(['cake_name', 'description', 'price', 'weight_grams', 'photo', 'is_active']);
        $this->existingImageUrl = null;
        $this->is_active        = true;
    }

    // ── Render ─────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.catalog.product-crud-form')
            ->layout('layouts.admin');
    }
}
