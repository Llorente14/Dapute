import mask    from '@alpinejs/mask';
import focus   from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

// Livewire v3 bundles and starts Alpine automatically.
// Register plugins into Livewire's Alpine instance via the alpine:init event.
// DO NOT import Alpine or call Alpine.start() here — that creates a second instance.
document.addEventListener('alpine:init', () => {
    Alpine.plugin(mask);
    Alpine.plugin(focus);
    Alpine.plugin(collapse);

    Alpine.data('profileAddressManager', (userId) => ({
        userId,
        addresses: [],
        formOpen: false,
        editingId: null,
        errors: {},
        form: {
            label: '',
            recipient_name: '',
            recipient_phone: '',
            address: '',
            city: '',
            postal_code: '',
            is_default: false,
        },

        init() {
            this.load();
        },

        get storageKey() {
            return `dapute_addresses_${this.userId}`;
        },

        get isFull() {
            return this.addresses.length >= 5;
        },

        load() {
            try {
                const stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                this.addresses = Array.isArray(stored) ? stored : [];
            } catch {
                this.addresses = [];
            }
            this.normalizeDefault();
        },

        persist() {
            localStorage.setItem(this.storageKey, JSON.stringify(this.addresses));
        },

        emptyForm() {
            return {
                label: '',
                recipient_name: '',
                recipient_phone: '',
                address: '',
                city: '',
                postal_code: '',
                is_default: this.addresses.length === 0,
            };
        },

        openCreate() {
            if (this.isFull) return;
            this.editingId = null;
            this.form = this.emptyForm();
            this.errors = {};
            this.formOpen = true;
        },

        openEdit(address) {
            this.editingId = address.id;
            this.form = {
                label: address.label || '',
                recipient_name: address.recipient_name || '',
                recipient_phone: address.recipient_phone || '',
                address: address.address || '',
                city: address.city || '',
                postal_code: address.postal_code || '',
                is_default: Boolean(address.is_default),
            };
            this.errors = {};
            this.formOpen = true;
        },

        cancelForm() {
            this.formOpen = false;
            this.editingId = null;
            this.errors = {};
            this.form = this.emptyForm();
        },

        validate() {
            const errors = {};
            ['label', 'recipient_name', 'recipient_phone', 'address', 'city', 'postal_code'].forEach((field) => {
                if (!String(this.form[field] || '').trim()) {
                    errors[field] = 'Required';
                }
            });

            if (this.form.postal_code && !/^\d+$/.test(String(this.form.postal_code))) {
                errors.postal_code = 'Numeric only';
            }

            this.errors = errors;
            return Object.keys(errors).length === 0;
        },

        save() {
            if (!this.validate()) return;

            const payload = {
                label: this.form.label.trim(),
                recipient_name: this.form.recipient_name.trim(),
                recipient_phone: this.form.recipient_phone.trim(),
                address: this.form.address.trim(),
                city: this.form.city.trim(),
                postal_code: String(this.form.postal_code).trim(),
                is_default: Boolean(this.form.is_default),
            };

            if (payload.is_default || this.addresses.length === 0) {
                this.addresses = this.addresses.map((address) => ({ ...address, is_default: false }));
                payload.is_default = true;
            }

            if (this.editingId) {
                this.addresses = this.addresses.map((address) => (
                    address.id === this.editingId ? { ...address, ...payload } : address
                ));
            } else {
                this.addresses = [
                    ...this.addresses,
                    { id: crypto.randomUUID(), ...payload },
                ];
            }

            this.normalizeDefault();
            this.persist();
            this.cancelForm();
        },

        remove(id) {
            const removedDefault = this.addresses.some((address) => address.id === id && address.is_default);
            this.addresses = this.addresses.filter((address) => address.id !== id);

            if (removedDefault && this.addresses.length > 0) {
                this.addresses = this.addresses.map((address, index) => ({ ...address, is_default: index === 0 }));
            }

            this.normalizeDefault();
            this.persist();
        },

        setDefault(id) {
            this.addresses = this.addresses.map((address) => ({ ...address, is_default: address.id === id }));
            this.persist();
        },

        normalizeDefault() {
            if (this.addresses.length === 0) return;

            const defaultIndex = this.addresses.findIndex((address) => address.is_default);
            this.addresses = this.addresses.map((address, index) => ({
                ...address,
                is_default: defaultIndex === -1 ? index === 0 : index === defaultIndex,
            }));
        },
    }));

    Alpine.data('checkoutAddressSelector', (userId, initialName = '', selectedAddress = null) => ({
        userId,
        wire: null,
        addresses: [],
        selectedId: '',
        selectedAddress,
        manual: {
            label: 'Checkout',
            recipient_name: initialName || '',
            recipient_phone: '',
            address: '',
            city: '',
            postal_code: '',
            is_default: false,
        },

        init(wire) {
            this.wire = wire;
            this.load();

            const defaultAddress = this.addresses.find((address) => address.is_default) || this.addresses[0];
            if (defaultAddress) {
                this.select(defaultAddress);
            } else {
                this.syncManual();
            }
        },

        get storageKey() {
            return `dapute_addresses_${this.userId}`;
        },

        load() {
            try {
                const stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                this.addresses = Array.isArray(stored) ? stored : [];
            } catch {
                this.addresses = [];
            }
        },

        payload(address) {
            return {
                label: address.label || 'Checkout',
                recipient_name: address.recipient_name || '',
                recipient_phone: address.recipient_phone || '',
                address: address.address || '',
                city: address.city || '',
                postal_code: String(address.postal_code || ''),
            };
        },

        select(address) {
            this.selectedId = address.id;
            this.sync(address);
        },

        selectById() {
            const address = this.addresses.find((item) => String(item.id) === String(this.selectedId));
            if (address) {
                this.select(address);
            }
        },

        selectedAddressDetails() {
            return this.addresses.find((item) => String(item.id) === String(this.selectedId)) || null;
        },

        sync(address) {
            if (!this.wire) return;
            const payload = this.payload(address);

            if (this.selectedAddress !== null) {
                this.selectedAddress = payload;
            }

            const setAddress = typeof this.wire.$set === 'function'
                ? this.wire.$set.bind(this.wire)
                : (typeof this.wire.set === 'function' ? this.wire.set.bind(this.wire) : null);

            if (!setAddress) return;

            Promise.resolve(setAddress('selected_address', payload))
                .then(() => this.fetchCouriers());
        },

        fetchCouriers() {
            if (typeof this.wire.fetchCouriers === 'function') {
                return this.wire.fetchCouriers();
            }

            if (typeof this.wire.$call === 'function') {
                return this.wire.$call('fetchCouriers');
            }

            if (typeof this.wire.call === 'function') {
                return this.wire.call('fetchCouriers');
            }

            return null;
        },

        syncManual() {
            this.sync(this.manual);
        },
    }));
});
