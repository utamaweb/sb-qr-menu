<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk Menu</title>

    @include('backend.layout.partials.menu._head')
</head>

<body>
    {{-- @php
        // Dummy data kategori & produk
        $mappedData = [
            [
                'id' => 'burgers',
                'name' => 'Burgers',
                'desc' => 'Daging premium, roti lembut, rasa juara',
                'items' => [
                    [
                        'id' => 'b1',
                        'name' => 'Classic Burger',
                        'price' => 35000,
                        'desc' => 'Beef, keju, selada, tomat, saus spesial',
                        'img' => 'https://picsum.photos/seed/burger1/640/360',
                    ],
                    [
                        'id' => 'b2',
                        'name' => 'Cheese Burger',
                        'price' => 38000,
                        'desc' => 'Double cheese, beef juicy, pickles',
                        'img' => 'https://picsum.photos/seed/burger2/640/360',
                    ],
                    [
                        'id' => 'b3',
                        'name' => 'Spicy Chicken Burger',
                        'price' => 36000,
                        'desc' => 'Ayam crispy pedas, mayo, kol',
                        'img' => 'https://picsum.photos/seed/burger3/640/360',
                    ],
                ],
            ],
            [
                'id' => 'pizzas',
                'name' => 'Pizzas',
                'desc' => 'Adonan tipis, topping melimpah',
                'items' => [
                    [
                        'id' => 'p1',
                        'name' => 'Margherita',
                        'price' => 55000,
                        'desc' => 'Tomat, mozarella, basil',
                        'img' => 'https://picsum.photos/seed/pizza1/640/360',
                    ],
                    [
                        'id' => 'p2',
                        'name' => 'Meat Lovers',
                        'price' => 68000,
                        'desc' => 'Pepperoni, sosis, smoked beef',
                        'img' => 'https://picsum.photos/seed/pizza2/640/360',
                    ],
                    [
                        'id' => 'p3',
                        'name' => 'Chicken BBQ',
                        'price' => 62000,
                        'desc' => 'Ayam BBQ, bawang, paprika',
                        'img' => 'https://picsum.photos/seed/pizza3/640/360',
                    ],
                ],
            ],
            [
                'id' => 'drinks',
                'name' => 'Minuman',
                'desc' => 'Segarkan hari Anda',
                'items' => [
                    [
                        'id' => 'd1',
                        'name' => 'Lemon Tea',
                        'price' => 18000,
                        'desc' => 'Teh dengan perasan lemon asli',
                        'img' => 'https://picsum.photos/seed/drink1/640/360',
                    ],
                    [
                        'id' => 'd2',
                        'name' => 'Iced Coffee',
                        'price' => 22000,
                        'desc' => 'Kopi susu dingin, creamy',
                        'img' => 'https://picsum.photos/seed/drink2/640/360',
                    ],
                    [
                        'id' => 'd3',
                        'name' => 'Mineral Water',
                        'price' => 8000,
                        'desc' => 'Air mineral dingin',
                        'img' => 'https://picsum.photos/seed/drink3/640/360',
                    ],
                ],
            ],
            [
                'id' => 'desserts',
                'name' => 'Desserts',
                'desc' => 'Manisnya pas, penutup sempurna',
                'items' => [
                    [
                        'id' => 's1',
                        'name' => 'Chocolate Lava Cake',
                        'price' => 28000,
                        'desc' => 'Coklat lumer hangat',
                        'img' => 'https://picsum.photos/seed/sweet1/640/360',
                    ],
                    [
                        'id' => 's2',
                        'name' => 'Cheese Cake',
                        'price' => 30000,
                        'desc' => 'Lembut, creamy, nagih',
                        'img' => 'https://picsum.photos/seed/sweet2/640/360',
                    ],
                    [
                        'id' => 's3',
                        'name' => 'Brownies',
                        'price' => 20000,
                        'desc' => 'Fudgy, intens coklat',
                        'img' => 'https://picsum.photos/seed/sweet3/640/360',
                    ],
                ],
            ],
        ];
    @endphp --}}

    <!-- Header -->
    <header class="app-header">
        <div class="container py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="brand">
                    <img src="{{ asset('logo/sb-logo.png') }}" alt="Logo SB" class="brand-badge">
                    <div>
                        <div class="fw-bold" style="letter-spacing:.2px;font-size:1.1rem">SB Kiosk</div>
                        <div class="text-secondary" style="font-size:.85rem">Pesan cepat, tanpa antre</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Category Nav -->
    <div class="category-nav">
        <div class="container py-2">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($mappedData as $cat)
                    <a href="#cat-{{ $cat['id'] }}"
                        class="category-pill text-decoration-none">{{ $cat['name'] }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="container py-4">
        @foreach ($mappedData as $cat)
            <section id="cat-{{ $cat['id'] }}" class="pt-2 pb-4">
                <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="section-title mb-1">{{ $cat['name'] }}</h2>
                        <div class="section-sub">{{ $cat['desc'] }}</div>
                    </div>
                </div>

                <div class="row g-3 g-md-4">
                    @foreach ($cat['items'] as $item)
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <div class="product-card h-100 d-flex flex-column" data-product
                                data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}"
                                data-price="{{ $item['price'] }}" data-category="{{ $cat['name'] }}">
                                <div class="product-media">
                                    <img class="product-img" src="{{ $item['img'] }}" alt="{{ $item['name'] }}">
                                    <span class="badge-price">Rp
                                        {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                                <div class="p-3 p-md-3 d-flex flex-column gap-2 flex-grow-1">
                                    <div>
                                        <div class="product-title">{{ $item['name'] }}</div>
                                        <div class="product-desc">{{ $item['desc'] }}</div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-auto">
                                        <div class="product-meta">Kategori: {{ $cat['name'] }}</div>
                                        <div class="qty-wrap" data-qty>
                                            <button class="qty-btn minus ring" aria-label="Kurangi" data-action="minus"
                                                data-target="{{ $item['id'] }}">–</button>
                                            <div class="qty-num" id="qty-{{ $item['id'] }}">0</div>
                                            <button class="qty-btn plus ring" aria-label="Tambah" data-action="plus"
                                                data-target="{{ $item['id'] }}">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </main>

    <!-- Bottom Spacer (height synced with cart bar) -->
    <div id="bottomSpacer" class="bottom-spacer"></div>

    <!-- Sticky Cart Bar -->
    <div class="cart-bar">
        <div class="container">
            <div class="cart-panel">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <span class="chip"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 7h14l-1.5 9.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.5L5 4H2" stroke="#fff"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg><span id="cartCount">0</span> item</span>
                    <span class="chip">Total <strong id="cartTotal">Rp 0</strong></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-light" id="resetBtn" type="button">Bersihkan</button>
                    <button class="btn btn-checkout" id="checkoutBtn" type="button" disabled>Pesan Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#e6eef7">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="checkoutLabel">Ringkasan Pesanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="checkoutList" class="vstack gap-2"></div>
                    <hr class="border-secondary-subtle">
                    <div class="d-flex justify-content-between">
                        <div class="text-secondary">Total</div>
                        <div class="fw-bold" id="checkoutTotal">Rp 0</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-checkout" id="confirmBtn">Konfirmasi Pesanan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden order form (change action to your submit route) -->
    <form id="orderForm" method="POST" action="{{ url()->current() }}" class="d-none">
        @csrf
        <input type="hidden" name="cart" id="orderCart" />
        <input type="hidden" name="total" id="orderTotal" />
    </form>

    @include('backend.layout.partials.menu._foot')

</body>

</html>
