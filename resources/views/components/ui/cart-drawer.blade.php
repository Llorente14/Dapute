<style>
    .qty-btn:hover {
        background-color: #d8e2dc;
    }
    .qty-btn:active {
        background-color: #bfc9c3;
    }
    .qty-btn.pressed {
        background-color: #012d1d;
        color: #f4fbf7;
        transform: scale(0.88);
    }
    .qty-btn.pressed span {
        color: #f4fbf7;
    }
    /* Hide native number spinner arrows */
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .qty-input[type=number] { -moz-appearance: textfield; }
    .qty-input:focus { background-color: #eef5f1; }
</style>

<div x-data="cartDrawer()"
     x-on:open-cart.window="isOpen = true"
     x-on:keydown.escape.window="isOpen = false"
     class="relative z-50">
    
    <!-- Overlay Dimmer (No blur as requested) -->
    <div class="fixed inset-0 bg-[#000000]/40 z-40" 
         x-show="isOpen" 
         x-transition.opacity.duration.300ms
         @click="isOpen = false"
         style="display: none;"></div>

    <!-- Cart Sidebar -->
    <aside class="fixed top-0 right-0 h-full w-full max-w-[460px] bg-[#f4fbf7] border-l-[2px] border-[#012d1d] z-50 shadow-[-4px_0px_0px_0px_rgba(1,45,29,1)] flex flex-col"
           x-show="isOpen"
           x-transition:enter="transition transform ease-out duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform ease-in duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           style="display: none;">

        <!-- Sidebar Header -->
        <header class="flex justify-between items-center p-6 border-b-[3px] border-[#012d1d] bg-[#eef5f1]">
            <h2 class="font-label font-bold text-[16px] text-[#012d1d] uppercase tracking-widest">Your Basket</h2>
            <button @click="isOpen = false" aria-label="Close Cart" class="text-[#012d1d] hover:bg-[#d8e2dc] p-2 border-[3px] border-transparent hover:border-[#012d1d] transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined" data-icon="close">close</span>
            </button>
        </header>

        <!-- Cart Items Scrollable Area -->
        <div class="flex-grow overflow-y-auto p-6 space-y-6">
            <template x-if="items.length > 0">
                <div>
                    <template x-for="(item, index) in items" :key="item.id">
                        <!-- Cart Item -->
                        <div class="flex gap-4 border-[3px] border-[#012d1d] bg-[#ffffff] p-4 relative shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] mb-6">
                            <!-- Item Image -->
                            <div class="w-[72px] h-[72px] border-[3px] border-[#012d1d] bg-[#e8efec] shrink-0 overflow-hidden">
                                <img :alt="item.cake_name" class="w-full h-full object-cover" :src="item.thumbnail" />
                            </div>

                            <!-- Item Details -->
                            <div class="flex flex-col flex-grow justify-between">
                                <div class="flex justify-between items-start gap-2">
                                    <div>
                                        <h3 class="font-headline font-bold text-[14px] text-[#012d1d] mb-1" x-text="item.cake_name"></h3>
                                        <p class="font-body text-[12px] text-[#57615c]" x-text="item.description"></p>
                                    </div>
                                    <button @click="removeItem(index)" class="text-[#57615c] hover:text-[#ba1a1a] transition-colors mt-[-4px] mr-[-4px] p-1">
                                        <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                                    </button>
                                </div>

                                <div class="flex justify-between items-end mt-4">
                                    <!-- Quantity Control -->
                                    <div class="flex items-stretch border-[2px] border-[#012d1d] bg-[#f4fbf7] overflow-hidden" style="height:32px">
                                        <!-- Decrease button -->
                                        <button
                                            @click="updateQuantity(index, -1); flashBtn($event.currentTarget)"
                                            class="qty-btn w-[32px] flex items-center justify-center text-[#012d1d] border-r-[2px] border-[#012d1d] select-none"
                                            style="transition: background 80ms, transform 80ms, box-shadow 80ms;"
                                        >
                                            <span class="material-symbols-outlined" style="font-size:15px; font-weight:700;">remove</span>
                                        </button>
                                        <!-- Quantity input (editable) -->
                                        <input
                                            type="number"
                                            min="1"
                                            max="99"
                                            :value="item.quantity"
                                            @focus="$event.target.select()"
                                            @input="if ($event.target.value.length > 2) $event.target.value = $event.target.value.slice(0, 2)"
                                            @blur="setQuantityFromInput($event, index)"
                                            @keydown.enter="$event.target.blur()"
                                            @keydown.escape="$event.target.value = item.quantity; $event.target.blur()"
                                            class="qty-input font-label font-bold text-[14px] min-w-[36px] w-[36px] text-center text-[#012d1d] bg-transparent border-none outline-none"
                                        />
                                        <!-- Increase button -->
                                        <button
                                            @click="updateQuantity(index, 1); flashBtn($event.currentTarget)"
                                            class="qty-btn w-[32px] flex items-center justify-center text-[#012d1d] border-l-[2px] border-[#012d1d] select-none"
                                            style="transition: background 80ms, transform 80ms, box-shadow 80ms;"
                                        >
                                            <span class="material-symbols-outlined" style="font-size:15px; font-weight:700;">add</span>
                                        </button>
                                    </div>
                                    <span class="font-label font-bold text-[#012d1d] text-[15px]" x-text="formatRupiah(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Empty State -->
            <template x-if="items.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-center space-y-4 py-12">
                    <span class="material-symbols-outlined text-[64px] text-[#012d1d]/30">shopping_cart</span>
                    <p class="font-body text-[#012d1d] font-bold">Your cart is currently empty</p>
                    <a href="/catalog" @click="isOpen = false" class="bg-[#012d1d] text-[#ffffff] font-label font-bold uppercase py-3 px-6 border-[3px] border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] btn-architectural hover:bg-[#1b4332] transition-all">
                        Start Shopping
                    </a>
                </div>
            </template>
        </div>

        <!-- Sidebar Footer / Checkout Actions -->
        <template x-if="items.length > 0">
            <footer class="border-t-[3px] border-[#012d1d] bg-[#f4fbf7] p-6 space-y-4">
                <!-- Order Summary -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="font-label font-bold uppercase text-[#012d1d]">SUBTOTAL</span>
                        <span class="font-label font-bold text-[#012d1d]" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-label font-bold uppercase text-[#012d1d]">SHIPPING</span>
                        <span class="font-body italic text-[14px] text-[#012d1d]">Calculated at checkout</span>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-end border-t-[2px] border-[#012d1d] pt-4 mb-6">
                    <span class="font-headline font-bold text-[18px] text-[#012d1d]">Total</span>
                    <span class="font-headline font-black text-[20px] text-[#012d1d]" x-text="formatRupiah(subtotal)"></span>
                </div>

                <!-- Checkout Button -->
                <button class="w-full bg-[#012d1d] text-[#ffffff] font-label font-bold uppercase py-4 border-[3px] border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:bg-[#1b4332] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)] transition-all flex justify-center items-center gap-2">
                    CHECKOUT NOW
                    <span class="material-symbols-outlined" data-icon="arrow_forward">arrow_forward</span>
                </button>
                <p class="text-center font-body text-[11px] text-[#57615c] mt-3">Shipping costs calculated at the next step</p>
            </footer>
        </template>
    </aside>
</div>

@push('scripts')
<script>
    function cartDrawer() {
        return {
            isOpen: false,
            items: [
                {
                    id: 1,
                    cake_name: 'Heritage Sourdough',
                    description: '800G • Dark Bake',
                    price: 180000,
                    quantity: 1,
                    thumbnail: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAwQwnyqXexA0SCrqa5Kwd6XWtI-edKf3S9PhUTKbVgl4xoZ3vUScDL0sNssk432xSIS2sn7OuopA4IcCxIyHZD968eI8D3Enpr4nBPp5eD8qJQvaJsykmP75wmOHj1yPcpjMEU_00io_kmVZnV8dNUWPqFBiQC-YqwkJArFy6sSp0zuLs2ByN4EdhjBt2HLGxPAr15yES9bDNEI2abyvk_JqyY4DIijVPAQuEJPCd17rXr0o04iMPWWICKCW9Cdp8cOuiQ1SSgLbOe'
                },
                {
                    id: 2,
                    cake_name: 'Matcha Croissant',
                    description: 'Daily Specimen',
                    price: 35000,
                    quantity: 2,
                    thumbnail: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAedkl9k3zXzvwzXuoWHtg902JbP8dNUiCtW2LHl9wKeEUvcoJvumU-XVWXIG_E0zeDWUsdKswi2H5-ME-e0GPlK9qeR3vpxhWD1NsHl8EQGhD5w8rOLww8NzXVL3-8AUZit0m7_oAUbYgMubVaAzAUa-e2iRja5WOsEqAby_53wsfovP5JlNVQA8YXK9wU3dT6Pp4ng65lD8AyCJec-gNTffz_8QuPbFJfq6wVSuSBpD_wpkZmpT5HlIkV7d6tgkdRKBCH8P_qWQ5j'
                }
            ],
            
            get subtotal() {
                return this.items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
            },
            
            updateQuantity(index, change) {
                const newQuantity = this.items[index].quantity + change;
                if (newQuantity >= 1 && newQuantity <= 99) {
                    this.items[index].quantity = newQuantity;
                }
            },
            
            removeItem(index) {
                this.items.splice(index, 1);
            },
            
            flashBtn(el) {
                el.classList.add('pressed');
                setTimeout(() => el.classList.remove('pressed'), 150);
            },
            
            setQuantityFromInput(event, index) {
                const val = parseInt(event.target.value);
                if (!isNaN(val) && val >= 1 && val <= 99) {
                    this.items[index].quantity = val;
                } else if (!isNaN(val) && val > 99) {
                    this.items[index].quantity = 99;
                    event.target.value = 99;
                } else {
                    // Reset to previous valid value
                    event.target.value = this.items[index].quantity;
                }
            },
            
            formatRupiah(amount) {
                return 'Rp ' + amount.toLocaleString('id-ID');
            }
        };
    }
</script>
@endpush
