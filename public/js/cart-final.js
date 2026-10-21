(function () {
    'use strict';
    if (window.cartSystemFinalLoaded) return;
    window.cartSystemFinalLoaded = true;

    let queue = Promise.resolve();
    let pending = 0;
    let toastTimer;
    const activeForms = new WeakSet();

    window.updateCartIcon = function (count) {
        document.querySelectorAll('.cart-count, [data-cart-count]').forEach(badge => {
            badge.textContent = String(count);
            if (badge.classList.contains('cart-badge')) badge.style.display = count > 0 ? '' : 'none';
        });
        document.querySelectorAll('.cart-item-count').forEach(el => el.textContent = count + ' produk');
    };

    window.showSingleNotification = function (message, type = 'success') {
        document.querySelector('.cart-notification-toast')?.remove();
        clearTimeout(toastTimer);
        const toast = document.createElement('div');
        toast.className = 'cart-notification-toast' + (type === 'error' ? ' is-error' : '');
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.textContent = message;
        document.body.appendChild(toast);
        toastTimer = setTimeout(() => toast.remove(), 4500);
    };

    function updateTotals(data, item) {
        if (data.subtotal !== undefined && item) {
            item.querySelectorAll('.subtotal-amount').forEach(el => el.textContent = 'Rp' + data.subtotal);
        }
        if (data.total !== undefined) {
            document.querySelectorAll('.cart-total, .cart-subtotal').forEach(el => el.textContent = 'Rp' + data.total);
        }
        const count = data.cartCount ?? data.cart_count;
        if (typeof count === 'number') window.updateCartIcon(count);
    }

    function updateCheckoutState() {
        document.querySelectorAll('.btn-checkout').forEach(link => {
            link.setAttribute('aria-disabled', pending ? 'true' : 'false');
            link.classList.toggle('opacity-50', pending > 0);
        });
    }

    async function request(form, body) {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body
        });
        if (response.status === 401 || response.status === 419 || response.redirected) {
            throw new Error('Sesi Anda berakhir. Muat ulang halaman dan masuk kembali.');
        }
        const data = await response.json();
        if (!response.ok || !data.success) {
            const messages = { 'Insufficient stock': 'Stok produk tidak mencukupi.', 'Invalid quantity': 'Masukkan jumlah produk yang valid.' };
            throw new Error(messages[data.message] || data.message || 'Perubahan belum tersimpan. Silakan coba lagi.');
        }
        return data;
    }

    function submitCartForm(form) {
        if (activeForms.has(form) || !form.reportValidity()) return;
        const removing = form.matches('.remove-form');
        const updating = form.matches('.quantity-update-form');
        if (removing && !window.confirm('Hapus produk ini dari keranjang?')) return;
        const item = form.closest('.cart-item');
        const input = form.querySelector('input[name="quantity"]');
        const body = new FormData(form);
        const controls = Array.from((item || form).querySelectorAll('button, input:not([type="hidden"])'));
        const states = controls.map(control => control.disabled);
        const button = form.querySelector('button[type="submit"]');
        const original = button?.innerHTML;
        activeForms.add(form);
        pending++;
        updateCheckoutState();
        controls.forEach(control => control.disabled = true);
        form.setAttribute('aria-busy', 'true');
        if (button) button.textContent = removing ? 'Menghapus...' : updating ? 'Menyimpan...' : 'Menambah...';

        // Serialize session mutations so successive requests cannot overwrite one another.
        queue = queue.then(async () => {
            try {
                const data = await request(form, body);
                updateTotals(data, item);
                if (updating && input) input.dataset.committedQuantity = input.value;
                if (removing) {
                    item?.remove();
                    if (data.is_empty || data.cart_count === 0) window.location.reload();
                }
                window.showSingleNotification(removing ? 'Produk dihapus dari keranjang.' : updating ? 'Jumlah produk berhasil diperbarui.' : 'Produk berhasil ditambahkan ke keranjang.');
            } catch (error) {
                if (updating && input?.dataset.committedQuantity) input.value = input.dataset.committedQuantity;
                window.showSingleNotification(error instanceof SyntaxError || error instanceof TypeError ? 'Tidak dapat terhubung. Periksa koneksi Anda dan coba lagi.' : error.message, 'error');
            } finally {
                controls.forEach((control, index) => control.disabled = states[index]);
                if (button) button.innerHTML = original;
                form.removeAttribute('aria-busy');
                activeForms.delete(form);
                pending--;
                updateCheckoutState();
            }
        });
    }

    function initialize() {
        document.querySelectorAll('.quantity-update-form input[name="quantity"]').forEach(input => {
            input.dataset.committedQuantity = input.value;
        });
        document.addEventListener('submit', event => {
            if (!event.target.matches('.add-to-cart-form, .quantity-update-form, .remove-form')) return;
            event.preventDefault();
            submitCartForm(event.target);
        });
        document.addEventListener('change', event => {
            if (event.target.matches('.quantity-update-form input[name="quantity"]')) event.target.form.requestSubmit();
        });
        document.addEventListener('click', event => {
            if (pending && event.target.closest('.btn-checkout')) {
                event.preventDefault();
                window.showSingleNotification('Tunggu sebentar, keranjang sedang diperbarui.');
            }
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
    else initialize();
})();