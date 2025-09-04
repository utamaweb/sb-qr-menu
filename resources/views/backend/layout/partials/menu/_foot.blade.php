<script>
    // Util: format Rupiah
    function formatIDR(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value || 0);
    }

    const cart = new Map(); // key: productId -> {id,name,price,qty,category}

    function updateQtyDisplay(id, qty) {
        const el = document.getElementById('qty-' + id);
        if (el) el.textContent = qty;
    }

    function recalc() {
        let items = 0,
            total = 0;
        for (const [, item] of cart) {
            items += item.qty;
            total += item.qty * item.price;
        }
        document.getElementById('cartCount').textContent = items;
        document.getElementById('cartTotal').textContent = formatIDR(total);
        document.getElementById('checkoutBtn').disabled = items === 0;
        document.getElementById('resetBtn').disabled = items === 0;
        adjustFooterSpace();
    }

    function adjustQty(productId, delta) {
        const card = document.querySelector(`[data-product][data-id="${productId}"]`);
        if (!card) return;
        const price = Number(card.getAttribute('data-price')) || 0;
        const name = card.getAttribute('data-name');
        const category = card.getAttribute('data-category');
        const current = cart.get(productId) || {
            id: productId,
            name,
            price,
            qty: 0,
            category
        };
        current.qty = Math.max(0, Math.min(99, current.qty + delta));
        if (current.qty === 0) cart.delete(productId);
        else cart.set(productId, current);
        updateQtyDisplay(productId, current.qty);
        recalc();
    }

    // Event delegation for +/-
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const id = btn.getAttribute('data-target');
        if (!id) return;
        const action = btn.getAttribute('data-action');
        if (action === 'plus') adjustQty(id, +1);
        if (action === 'minus') adjustQty(id, -1);
    });

    // Reset cart
    document.getElementById('resetBtn').addEventListener('click', () => {
        for (const pid of cart.keys()) updateQtyDisplay(pid, 0);
        cart.clear();
        recalc();
    });

    // Checkout modal logic
    const checkoutBtn = document.getElementById('checkoutBtn');
    const checkoutModalEl = document.getElementById('checkoutModal');
    checkoutBtn.addEventListener('click', () => {
        const list = document.getElementById('checkoutList');
        list.innerHTML = '';
        let total = 0;
        const entries = Array.from(cart.values());
        entries.sort((a, b) => a.category.localeCompare(b.category) || a.name.localeCompare(b.name));
        let currentCat = '';
        for (const item of entries) {
            if (item.category !== currentCat) {
                currentCat = item.category;
                const catEl = document.createElement('div');
                catEl.className = 'mt-2 text-secondary fw-semibold';
                catEl.textContent = currentCat;
                list.appendChild(catEl);
            }
            const row = document.createElement('div');
            row.className =
                'd-flex align-items-center justify-content-between py-2 border-bottom border-secondary-subtle';
            row.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark">x${item.qty}</span>
                <span>${item.name}</span>
            </div>
            <div class="fw-semibold">${formatIDR(item.qty * item.price)}</div>
        `;
            list.appendChild(row);
            total += item.qty * item.price;
        }
        document.getElementById('checkoutTotal').textContent = formatIDR(total);
        // Bootstrap bundle is loaded before this script, so we can instantiate directly
        bootstrap.Modal.getOrCreateInstance(checkoutModalEl).show();
    });

    // Confirm: serialize and submit form
    document.getElementById('confirmBtn').addEventListener('click', () => {
        const entries = Array.from(cart.values());
        let total = 0;
        for (const it of entries) total += it.qty * it.price;

        const form = document.getElementById('orderForm');
        const cartField = document.getElementById('orderCart');
        const totalField = document.getElementById('orderTotal');
        if (!form || !cartField || !totalField) return;

        cartField.value = JSON.stringify(entries);
        totalField.value = String(total);

        // Optional UX: prevent double submit
        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = 'Memproses…';

        form.submit();
    });

    // Nav/header are fixed via CSS. No JS needed for positioning/highlight.

    // Initial setup early (don’t wait for images)
    document.addEventListener('DOMContentLoaded', () => {
        adjustFooterSpace();
    });

    // Recalculate after assets load (dimensions may change)
    window.addEventListener('load', adjustFooterSpace);
    window.addEventListener('resize', adjustFooterSpace);
</script>
