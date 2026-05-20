<?php

namespace App\Livewire;

use App\Actions\Cart\UpdateCartAction;
use App\Actions\Checkout\FetchBiteshipRatesAction;
use App\Actions\Transaction\CreateOrderAction;
use App\Actions\Payment\GetMidtransSnapTokenAction;
use App\Enums\ShippingType;
use App\Helpers\AddressManager;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CheckoutPage extends Component
{
    public array $items = [];
    public int $subtotal = 0;
    public string $recipient_name = '';
    public array $selected_address = [];

    public array $couriers = [];
    public ?string $selected_courier = null;
    public string $shipping_type = 'ONLINE_COURIER';
    public string $courier_type = 'regular';
    public int $shippingCost = 0;
    public int $adminFee = 2500;
    public int $total = 0;
    public ?string $order_id = null;
    public ?string $courierError = null;
    public string $notes = '';

    public bool $isProcessing = false;

    public function mount(): void
    {
        $this->loadCart();

        if (Auth::check()) {
            $this->recipient_name = Auth::user()->name ?? '';
        }
    }

    public function loadCart(?UpdateCartAction $action = null): void
    {
        if (!Auth::check()) {
            return;
        }

        $action ??= app(UpdateCartAction::class);
        $this->items = $action->getItems((string) Auth::id());

        $this->subtotal = collect($this->items)->sum(function ($item) {
            return (int) $item['price_snapshot'] * (int) $item['quantity'];
        });

        $this->calculateTotal();
    }

    public function updated($property, $value = null): void
    {
        if ($property === 'selected_address' || str_starts_with($property, 'selected_address.')) {
            $this->fetchCouriers();
        }

        if ($property === 'courier_type' || $property === 'shipping_type') {
            $this->selected_courier = null;
            $this->shippingCost = 0;
            $this->fetchCouriers();
        }

        if ($property === 'selected_courier') {
            $this->applySelectedCourier();
        }
    }

    public function fetchCouriers(?FetchBiteshipRatesAction $action = null): void
    {
        $postalCode = (string) ($this->selected_address['postal_code'] ?? '');

        if (!preg_match('/^\d{5}$/', $postalCode)) {
            $this->couriers = [];
            $this->selected_courier = null;
            $this->shippingCost = 0;
            $this->courierError = $postalCode === '' ? null : 'Postal code must contain exactly 5 digits.';
            $this->calculateTotal();
            return;
        }

        if ($this->shipping_type === ShippingType::INDEPENDENT->value) {
            $this->couriers = $this->independentCourierOptions();
            $this->courierError = null;
            $this->selected_courier ??= $this->couriers[0]['id'] ?? null;
            $this->applySelectedCourier();
            return;
        }

        if ($this->courier_type === 'instant' && !$this->hasDestinationCoordinate()) {
            $this->couriers = [];
            $this->selected_courier = null;
            $this->shippingCost = 0;
            $this->courierError = 'Pick a map pin or use your current location for instant courier rates.';
            $this->calculateTotal();
            return;
        }

        $action ??= app(FetchBiteshipRatesAction::class);
        $result = $action->execute((string) Auth::id(), $postalCode, $this->selected_address, $this->courier_type);

        if (!$result['success']) {
            $this->couriers = [];
            $this->selected_courier = null;
            $this->shippingCost = 0;
            $this->courierError = $result['message'] ?? 'Gagal mengambil tarif kurir.';
            $this->calculateTotal();
            return;
        }

        $this->couriers = collect($result['data'] ?? [])
            ->values()
            ->map(fn ($rate, $index) => $this->normalizeCourier((array) $rate, $index))
            ->toArray();

        $this->courierError = empty($this->couriers)
            ? 'Tarif kurir tidak tersedia untuk kode pos ini.'
            : null;

        if ($this->selected_courier && !collect($this->couriers)->firstWhere('id', $this->selected_courier)) {
            $this->selected_courier = null;
            $this->shippingCost = 0;
        }

        $this->applySelectedCourier();
    }

    public function applySelectedCourier(): void
    {
        $selected = collect($this->couriers)->firstWhere('id', $this->selected_courier);
        $this->shippingCost = $selected ? (int) $selected['price'] : 0;
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->total = $this->subtotal + $this->shippingCost + $this->adminFee;
    }

    public function setShippingType(string $shippingType): void
    {
        $this->shipping_type = ShippingType::tryFrom($shippingType)?->value
            ?? ShippingType::ONLINE_COURIER->value;

        $this->selected_courier = null;
        $this->shippingCost = 0;
        $this->fetchCouriers();
    }

    public function setShippingOption(string $shippingType, ?string $courierType = null): void
    {
        $this->shipping_type = ShippingType::tryFrom($shippingType)?->value
            ?? ShippingType::ONLINE_COURIER->value;

        if ($courierType) {
            $this->courier_type = in_array($courierType, ['regular', 'instant'], true)
                ? $courierType
                : 'regular';
        }

        $this->selected_courier = null;
        $this->shippingCost = 0;
        $this->fetchCouriers();
    }

    public function placeOrder(?CreateOrderAction $action = null, ?GetMidtransSnapTokenAction $snapAction = null): void
    {
        if ($this->isProcessing) {
            return;
        }

        if ($this->order_id) {
            $this->triggerSnap($snapAction);
            return;
        }

        $this->resetErrorBag();

        $addressValidation = AddressManager::validateAddress($this->selected_address);
        if (!$addressValidation['valid']) {
            $this->addError('selected_address', 'Please complete a valid shipping address.');

            foreach ($addressValidation['errors'] as $field => $messages) {
                $this->addError('selected_address.'.$field, $messages[0] ?? 'Invalid value.');
            }

            return;
        }

        $this->selected_address['recipient_phone'] = AddressManager::normalizePhoneNumber(
            (string) ($this->selected_address['recipient_phone'] ?? '')
        );

        if (!$this->selected_courier) {
            $this->addError('selected_courier', 'Pilih kurir terlebih dahulu.');
            return;
        }

        if ($this->shipping_type === ShippingType::ONLINE_COURIER->value && $this->courier_type === 'instant' && !$this->hasDestinationCoordinate()) {
            $this->addError('selected_address.coordinates', 'Pick a map pin for instant courier delivery.');
            return;
        }

        $selectedCourier = collect($this->couriers)->firstWhere('id', $this->selected_courier);
        if (!$selectedCourier) {
            $this->addError('selected_courier', 'Kurir tidak valid.');
            return;
        }

        $this->isProcessing = true;

        $notes = trim($this->notes) ?: null;

        $action ??= app(CreateOrderAction::class);
        $result = $action->execute(
            (string) Auth::id(),
            $this->shippingCost,
            $notes,
            $this->adminFee,
            $this->selected_address,
            $this->selectedShippingType()
        );

        $this->isProcessing = false;

        if (!$result['success']) {
            $this->addError('order', $result['message'] ?? 'Gagal membuat pesanan.');
            return;
        }

        $this->order_id = $result['order_id'];
        $this->triggerSnap($snapAction);
    }

    private function triggerSnap(?GetMidtransSnapTokenAction $snapAction = null): void
    {
        $snapAction ??= app(GetMidtransSnapTokenAction::class);
        $snapResult = $snapAction->execute($this->order_id);

        if ($snapResult['success']) {
            $this->dispatch('open-snap', token: $snapResult['snap_token'], order_id: $this->order_id);
        } else {
            $this->addError('order', $snapResult['message'] ?? 'Gagal mendapatkan token pembayaran.');
        }
    }

    public function processPayment(?CreateOrderAction $action = null): void
    {
        $this->placeOrder($action);
    }

    private function normalizeCourier(array $rate, int $index): array
    {
        $courierCode = $rate['courier_code'] ?? $rate['company'] ?? 'courier';
        $serviceCode = $rate['courier_service_code'] ?? $rate['service_code'] ?? $rate['service_type'] ?? $index;
        $price = (int) ($rate['price'] ?? $rate['final_price'] ?? 0);

        return [
            'id' => "{$courierCode}:{$serviceCode}:{$price}:{$index}",
            'name' => $rate['courier_name'] ?? $rate['company'] ?? strtoupper((string) $courierCode),
            'service' => $rate['courier_service_name'] ?? $rate['service_name'] ?? $rate['description'] ?? (string) $serviceCode,
            'estimate' => $rate['duration'] ?? $rate['shipment_duration_range'] ?? 'Estimasi tersedia',
            'price' => $price,
            'icon' => 'local_shipping',
        ];
    }

    private function independentCourierOptions(): array
    {
        return [
            [
                'id' => 'independent:self-pickup:0:0',
                'name' => 'Independent Driver',
                'service' => 'Self Pickup / Manual Courier',
                'estimate' => 'Handled by Dapute',
                'price' => 0,
                'icon' => 'storefront',
            ],
        ];
    }

    private function selectedShippingType(): string
    {
        return ShippingType::tryFrom($this->shipping_type)?->value
            ?? ShippingType::ONLINE_COURIER->value;
    }

    private function hasDestinationCoordinate(): bool
    {
        $coordinates = $this->selected_address['coordinates'] ?? null;

        if (is_string($coordinates)) {
            $coordinates = json_decode($coordinates, true);
        }

        if (!is_array($coordinates)) {
            return false;
        }

        $latitude = $coordinates['latitude'] ?? $coordinates['lat'] ?? null;
        $longitude = $coordinates['longitude'] ?? $coordinates['longtitude'] ?? $coordinates['lng'] ?? $coordinates['lon'] ?? null;

        return is_numeric($latitude) && is_numeric($longitude);
    }

    public function render()
    {
        return view('livewire.transaction.checkout-form')
            ->layout('layouts.checkout');
    }
}
