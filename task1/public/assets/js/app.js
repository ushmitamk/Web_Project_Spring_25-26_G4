(function () {
    const BASE = window.APP_BASE_URL || '';

    function money(value) {
        return '$' + Number(value || 0).toFixed(2);
    }

    async function jsonFetch(url, options) {
        const response = await fetch(BASE + url, options || {});
        const data = await response.json().catch(() => ({ ok: false, message: 'Invalid server response.' }));
        if (!response.ok || data.ok === false) {
            throw new Error(data.message || 'Request failed.');
        }
        return data;
    }

    function updateCartCount(count) {
        const el = document.getElementById('cart-count');
        if (el) el.textContent = count;
    }

    document.addEventListener('click', function (e) {
        const backBtn = e.target.closest('.page-back');
        if (!backBtn) return;
        e.preventDefault();
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = backBtn.dataset.fallback || (BASE + '/products');
        }
    });

    document.addEventListener('click', async function (e) {
        const addBtn = e.target.closest('.add-to-cart');
        if (addBtn) {
            e.preventDefault();
            try {
                addBtn.disabled = true;
                const data = await jsonFetch('/api/cart/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: addBtn.dataset.productId })
                });
                updateCartCount(data.cart_count);
                addBtn.textContent = 'Added';
                setTimeout(() => { addBtn.textContent = 'Add to Cart'; addBtn.disabled = false; }, 800);
            } catch (err) {
                alert(err.message);
                addBtn.disabled = false;
            }
        }

        const qtyBtn = e.target.closest('.qty-btn');
        if (qtyBtn) {
            const row = qtyBtn.closest('tr');
            const productId = row.dataset.productId;
            const qtyEl = row.querySelector('.qty');
            const current = parseInt(qtyEl.textContent, 10);
            const newQty = qtyBtn.dataset.action === 'plus' ? current + 1 : current - 1;
            try {
                const data = await jsonFetch('/api/cart/update', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId, quantity: newQty })
                });
                if (data.quantity <= 0) row.remove(); else {
                    qtyEl.textContent = data.quantity;
                    row.querySelector('.line-total').textContent = money(data.line_total);
                }
                document.getElementById('grand-total').textContent = money(data.grand_total);
                updateCartCount(data.cart_count);
                if (data.cart_count <= 0) {
                    document.getElementById('cart-area').innerHTML = '<p class="empty">Your cart is empty.</p><a class="btn" href="' + BASE + '/products">Browse Products</a>';
                }
            } catch (err) { alert(err.message); }
        }

        const removeBtn = e.target.closest('.remove-cart');
        if (removeBtn) {
            const row = removeBtn.closest('tr');
            const productId = row.dataset.productId;
            try {
                const data = await jsonFetch('/api/cart/remove', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                });
                row.remove();
                document.getElementById('grand-total').textContent = money(data.grand_total);
                updateCartCount(data.cart_count);
                if (data.cart_count <= 0) {
                    document.getElementById('cart-area').innerHTML = '<p class="empty">Your cart is empty.</p><a class="btn" href="' + BASE + '/products">Browse Products</a>';
                }
            } catch (err) { alert(err.message); }
        }

        const toggleOrder = e.target.closest('.toggle-order');
        if (toggleOrder) {
            document.getElementById(toggleOrder.dataset.target).classList.toggle('hidden');
        }

        const badge = e.target.closest('.availability-badge');
        if (badge) {
            try {
                const data = await jsonFetch('/api/products/' + badge.dataset.productId + '/availability', { method: 'PATCH' });
                badge.textContent = data.text;
                badge.classList.toggle('in', data.is_available === 1);
                badge.classList.toggle('out', data.is_available !== 1);
            } catch (err) { alert(err.message); }
        }
    });

    const searchInput = document.getElementById('product-search');
    const categoryFilter = document.getElementById('category-filter');
    const productGrid = document.getElementById('product-grid');
    let timer = null;
    async function loadProducts() {
        if (!productGrid) return;
        const q = searchInput ? searchInput.value.trim() : '';
        const cat = categoryFilter ? categoryFilter.value : '';
        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (cat) params.set('category_id', cat);
        const endpoint = q ? '/api/products/search?' + params.toString() : '/api/products?' + params.toString();
        try {
            const data = await jsonFetch(endpoint);
            productGrid.innerHTML = data.html;
        } catch (err) {
            productGrid.innerHTML = '<div class="empty">' + err.message + '</div>';
        }
    }
    if (searchInput) searchInput.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(loadProducts, 250); });
    if (categoryFilter) categoryFilter.addEventListener('change', loadProducts);

    document.addEventListener('change', async function (e) {
        const select = e.target.closest('.admin-status-select');
        if (!select) return;
        const row = select.closest('tr');
        const msg = row.querySelector('.status-message');
        try {
            const data = await jsonFetch('/api/orders/' + row.dataset.orderId, {
                method: 'PUT', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: select.value })
            });
            const badge = row.querySelector('.order-status');
            badge.textContent = data.status;
            badge.className = 'order-status badge badge-' + data.status.toLowerCase();
            msg.textContent = 'Saved';
            msg.className = 'status-message ok';
        } catch (err) {
            msg.textContent = err.message;
            msg.className = 'status-message field-error';
        }
    });

    const reviewsBox = document.getElementById('product-reviews');
    async function loadReviews() {
        if (!reviewsBox) return;
        try {
            const data = await jsonFetch('/api/products/' + reviewsBox.dataset.productId + '/reviews');
            const ratingEl = document.getElementById('detail-rating');
            if (ratingEl) ratingEl.textContent = '★ ' + data.average + ' / 5';
            if (!data.reviews.length) {
                reviewsBox.innerHTML = '<p class="empty">No reviews yet.</p>';
                return;
            }
            reviewsBox.innerHTML = data.reviews.map(r => '<div class="review"><strong>' + escapeHtml(r.reviewer_name) + '</strong> <span>★ ' + r.rating + '</span><p>' + escapeHtml(r.review_text) + '</p><small>' + escapeHtml(r.created_at) + '</small></div>').join('');
        } catch (err) { reviewsBox.textContent = err.message; }
    }
    loadReviews();

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"]/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[s]));
    }

    document.addEventListener('submit', async function (e) {
        const form = e.target.closest('.review-form');
        if (!form) return;
        e.preventDefault();
        const msg = form.querySelector('.review-message');
        const formData = new FormData(form);
        try {
            await jsonFetch('/api/reviews', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: form.dataset.productId, rating: formData.get('rating'), review_text: formData.get('review_text') })
            });
            msg.textContent = 'Review submitted.';
            msg.className = 'review-message ok';
            form.querySelector('button').disabled = true;
            form.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
            if (reviewsBox && String(reviewsBox.dataset.productId) === String(form.dataset.productId)) {
                loadReviews();
            }
        } catch (err) {
            msg.textContent = err.message;
            msg.className = 'review-message field-error';
        }
    });
})();
