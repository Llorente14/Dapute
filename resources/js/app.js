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

        digitsOnly(value, limit = null) {
            const digits = String(value || '').replace(/\D/g, '');
            return limit ? digits.slice(0, limit) : digits;
        },

        phoneLocalDigits(value) {
            let digits = this.digitsOnly(value);
            if (digits.startsWith('62')) digits = digits.slice(2);
            if (digits.startsWith('0')) digits = digits.slice(1);
            return digits.slice(0, 11);
        },

        fullIndonesianPhone(value) {
            const local = this.phoneLocalDigits(value);
            return local ? `+62${local}` : '';
        },

        cleanPhoneNumber() {
            this.form.recipient_phone = this.phoneLocalDigits(this.form.recipient_phone);
        },

        cleanPostalCode() {
            this.form.postal_code = this.digitsOnly(this.form.postal_code, 5);
        },

        load() {
            try {
                const stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                this.addresses = Array.isArray(stored)
                    ? stored.map((address) => ({
                        ...address,
                        recipient_phone: this.fullIndonesianPhone(address.recipient_phone || ''),
                    }))
                    : [];
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
                recipient_phone: this.phoneLocalDigits(address.recipient_phone || ''),
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

            const phone = String(this.form.recipient_phone || '').trim();
            if (!errors.recipient_phone && !/^\d{8,11}$/.test(phone)) {
                errors.recipient_phone = 'Use 8-11 digits after +62';
            }

            const postalCode = String(this.form.postal_code || '').trim();
            if (!errors.postal_code && !/^\d{5}$/.test(postalCode)) {
                errors.postal_code = 'Use exactly 5 digits';
            }

            this.errors = errors;
            return Object.keys(errors).length === 0;
        },

        save() {
            if (!this.validate()) return;

            const payload = {
                label: this.form.label.trim(),
                recipient_name: this.form.recipient_name.trim(),
                recipient_phone: this.fullIndonesianPhone(this.form.recipient_phone),
                address: this.form.address.trim(),
                city: this.form.city.trim(),
                postal_code: this.digitsOnly(this.form.postal_code, 5),
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

    Alpine.data('checkoutAddressSelector', (userId, initialName = '', selectedAddress = null, courierType = 'regular') => ({
        userId,
        wire: null,
        addresses: [],
        selectedId: '',
        selectedAddress,
        courierType,
        map: null,
        mapElement: null,
        marker: null,
        leafletLoading: null,
        coordinateStatus: '',
        manual: {
            label: 'Checkout',
            recipient_name: initialName || '',
            recipient_phone: '',
            address: '',
            city: '',
            postal_code: '',
            coordinates: null,
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

            if (this.isInstant()) {
                setTimeout(() => this.initCoordinatePicker(), 200);
            }
        },

        get storageKey() {
            return `dapute_addresses_${this.userId}`;
        },

        load() {
            try {
                const stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                this.addresses = Array.isArray(stored)
                    ? stored.map((address) => ({
                        ...address,
                        recipient_phone: this.fullIndonesianPhone(address.recipient_phone || ''),
                    }))
                    : [];
            } catch {
                this.addresses = [];
            }
        },

        payload(address) {
            return {
                label: address.label || 'Checkout',
                recipient_name: address.recipient_name || '',
                recipient_phone: this.fullIndonesianPhone(address.recipient_phone || ''),
                address: address.address || '',
                city: address.city || '',
                postal_code: String(address.postal_code || ''),
                coordinates: address.coordinates || null,
            };
        },

        select(address) {
            this.selectedId = address.id;
            this.sync(address);

            if (this.isInstant()) {
                setTimeout(() => this.initCoordinatePicker(), 100);
            }
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

        isInstant() {
            return this.courierType === 'instant';
        },

        digitsOnly(value, limit = null) {
            const digits = String(value || '').replace(/\D/g, '');
            return limit ? digits.slice(0, limit) : digits;
        },

        phoneLocalDigits(value) {
            let digits = this.digitsOnly(value);
            if (digits.startsWith('62')) digits = digits.slice(2);
            if (digits.startsWith('0')) digits = digits.slice(1);
            return digits.slice(0, 11);
        },

        fullIndonesianPhone(value) {
            const local = this.phoneLocalDigits(value);
            return local ? `+62${local}` : '';
        },

        cleanManualPhone() {
            this.manual.recipient_phone = this.phoneLocalDigits(this.manual.recipient_phone);
        },

        cleanManualPostalCode() {
            this.manual.postal_code = this.digitsOnly(this.manual.postal_code, 5);
        },

        setCourierType(type) {
            this.courierType = type;
            this.selectedAddress = this.selectedAddress || {};

            if (this.isInstant()) {
                setTimeout(() => this.initCoordinatePicker(), 100);
                this.geocodeAddress(false);
            } else {
                this.fetchCouriers();
            }
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

        currentAddress() {
            return this.selectedAddressDetails() || this.manual || {};
        },

        currentCoordinates() {
            const raw = this.currentAddress().coordinates || this.selectedAddress?.coordinates || null;
            if (!raw) return null;

            const latitude = Number(raw.latitude ?? raw.lat);
            const longitude = Number(raw.longitude ?? raw.longtitude ?? raw.lng ?? raw.lon);

            return Number.isFinite(latitude) && Number.isFinite(longitude)
                ? { latitude, longitude }
                : null;
        },

        ensureLeaflet() {
            if (window.L) return Promise.resolve();
            if (this.leafletLoading) return this.leafletLoading;

            this.coordinateStatus = 'Loading map...';

            this.leafletLoading = new Promise((resolve, reject) => {
                if (!document.querySelector('link[data-dapute-leaflet]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    link.setAttribute('data-dapute-leaflet', 'true');
                    document.head.appendChild(link);
                }

                const finish = () => {
                    if (window.L) {
                        resolve();
                    } else {
                        reject(new Error('Leaflet did not load.'));
                    }
                };

                const existingScript = document.querySelector('script[data-dapute-leaflet]');
                if (existingScript) {
                    existingScript.addEventListener('load', finish, { once: true });
                    existingScript.addEventListener('error', reject, { once: true });
                    setTimeout(finish, 800);
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.defer = true;
                script.setAttribute('data-dapute-leaflet', 'true');
                script.onload = finish;
                script.onerror = reject;
                document.head.appendChild(script);
            });

            return this.leafletLoading;
        },

        initCoordinatePicker(attempt = 0) {
            if (!this.isInstant()) return;

            if (!window.L) {
                this.ensureLeaflet()
                    .then(() => this.initCoordinatePicker(attempt + 1))
                    .catch(() => {
                        this.coordinateStatus = 'Map failed to load. Check the connection, then try again.';
                    });
                return;
            }

            const mapElement = this.$refs.coordinateMap;
            if (!mapElement || !mapElement.isConnected || mapElement.offsetParent === null) {
                if (attempt < 12) {
                    setTimeout(() => this.initCoordinatePicker(attempt + 1), 150);
                }
                return;
            }

            const coords = this.currentCoordinates() || { latitude: -6.2, longitude: 106.816666 };
            this.coordinateStatus = this.currentCoordinates()
                ? 'Pin ready. Drag marker to fine tune delivery point.'
                : 'Move the pin, use your location, or geocode the address.';

            setTimeout(() => {
                const liveMapElement = this.$refs.coordinateMap;
                if (!liveMapElement || !liveMapElement.isConnected || liveMapElement.offsetParent === null) {
                    if (attempt < 12) {
                        setTimeout(() => this.initCoordinatePicker(attempt + 1), 150);
                    }
                    return;
                }

                if (this.map && this.mapElement !== liveMapElement) {
                    this.map.remove();
                    this.map = null;
                    this.marker = null;
                }

                if (!this.map) {
                    this.mapElement = liveMapElement;
                    delete liveMapElement._leaflet_id;
                    this.map = window.L.map(liveMapElement).setView([coords.latitude, coords.longitude], 15);
                    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap',
                    }).addTo(this.map);

                    this.marker = window.L.marker([coords.latitude, coords.longitude], { draggable: true }).addTo(this.map);
                    this.marker.on('dragend', () => {
                        const point = this.marker.getLatLng();
                        this.updateCoordinate(point.lat, point.lng, 'Pin moved.');
                    });
                } else {
                    this.map.setView([coords.latitude, coords.longitude], 15);
                    if (this.marker) {
                        this.marker.setLatLng([coords.latitude, coords.longitude]);
                    }
                }

                requestAnimationFrame(() => {
                    this.map.invalidateSize();
                    setTimeout(() => this.map?.invalidateSize(), 250);
                });

                if (!this.currentCoordinates()) {
                    this.geocodeAddress(false);
                }
            }, 120);
        },

        updateCoordinate(latitude, longitude, message = 'Coordinate selected.') {
            const coordinate = {
                latitude: Number(latitude.toFixed(7)),
                longitude: Number(longitude.toFixed(7)),
                longtitude: Number(longitude.toFixed(7)),
            };

            if (this.selectedId) {
                this.addresses = this.addresses.map((address) => String(address.id) === String(this.selectedId)
                    ? { ...address, coordinates: coordinate }
                    : address);
                localStorage.setItem(this.storageKey, JSON.stringify(this.addresses));
                this.sync(this.selectedAddressDetails());
            } else {
                this.manual = { ...this.manual, coordinates: coordinate };
                this.syncManual();
            }

            if (this.marker) {
                this.marker.setLatLng([coordinate.latitude, coordinate.longitude]);
            }

            this.coordinateStatus = `${message} Lat ${coordinate.latitude}, Lng ${coordinate.longitude}`;
        },

        useMyLocation() {
            if (!navigator.geolocation) {
                this.coordinateStatus = 'Geolocation is not supported by this browser.';
                return;
            }

            this.coordinateStatus = 'Reading browser location...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    this.updateCoordinate(latitude, longitude, 'Current location selected.');
                    if (this.map) this.map.setView([latitude, longitude], 16);
                },
                () => {
                    this.coordinateStatus = 'Unable to read current location. Drag the pin manually.';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        async geocodeAddress(showStatus = true) {
            const address = this.currentAddress();
            const query = [address.address, address.city, address.postal_code]
                .filter(Boolean)
                .join(', ');

            if (!query) {
                if (showStatus) this.coordinateStatus = 'Address is empty. Drag the pin manually.';
                return;
            }

            if (showStatus) this.coordinateStatus = 'Searching coordinate from address...';

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`);
                const results = await response.json();

                if (!results.length) {
                    if (showStatus) this.coordinateStatus = 'Coordinate not found. Drag the pin manually.';
                    return;
                }

                const latitude = Number(results[0].lat);
                const longitude = Number(results[0].lon);
                this.updateCoordinate(latitude, longitude, 'Coordinate found from address.');
                if (this.map) this.map.setView([latitude, longitude], 16);
            } catch {
                if (showStatus) this.coordinateStatus = 'Geocoding failed. Drag the pin manually.';
            }
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
            this.cleanManualPhone();
            this.cleanManualPostalCode();
            this.sync(this.manual);
        },
    }));
});
