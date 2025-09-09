<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk Menu (Mobile)</title>

    @include('backend.layout.partials.menu._head')

    <style>
        /* Mobile-first overrides (portrait) */
        :root {
            --header-height: 56px;
            --nav-height: 52px;
            --anchor-offset: calc(var(--header-height) + var(--nav-height) + 8px);
        }

        /* tighter paddings on mobile */
        .app-header .container {
            padding-left: .75rem;
            padding-right: .75rem;
        }

        .category-nav .container {
            padding-left: .5rem;
            padding-right: .5rem;
            position: relative;
        }

        /* horizontal scrollable category pills */
        .category-nav .d-flex {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        /* hide scrollbar - WebKit */
        .category-nav .d-flex::-webkit-scrollbar {
            display: none;
        }

        .category-nav .category-pill {
            flex: 0 0 auto;
            transition: background-color .2s ease, color .2s ease, transform .1s ease;
        }

        /* product media shorter to fit viewport */
        .product-img {
            height: 140px;
        }

        /* list layout: one product per row */
        .product-row {
            display: flex;
            gap: .75rem;
            align-items: center;
            padding: .65rem .75rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, .03);
            border: 1px solid rgba(255, 255, 255, .06);
        }

        .product-row:hover {
            background: rgba(255, 255, 255, .05);
        }

        .product-thumb {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            object-fit: cover;
            background: linear-gradient(180deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, .04));
        }

        .product-info .product-title {
            font-weight: 600;
            line-height: 1.2;
        }

        .product-info .product-price {
            color: #e6eef7;
        }

        .product-info .product-meta {
            font-size: .8rem;
        }

        .qty-wrap {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        /* middle column layout: title on top; below it left (category+price) and right (buttons) */
        .product-sub {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: .35rem;
        }

        .product-sub-left {
            display: flex;
            flex-direction: column;
            gap: .1rem;
        }

        /* icon-only buttons for cart bar */
        .btn-icon {
            width: 44px;
            height: 44px;
            padding: 0;
            display: grid;
            place-items: center;
            border-radius: 12px;
        }

        /* slightly smaller tap targets (still >=40px for ergonomics) */
        .qty-btn {
            width: 40px;
            height: 40px;
            font-size: 1.05rem;
        }

        .category-pill {
            padding: .5rem .7rem;
            font-size: .9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, .06);
            color: #e8eef6;
        }

        /* keep hover/active simple (no highlight for simplicity) */
        .category-pill:focus-visible {
            outline: 2px solid rgba(255, 255, 255, .15);
            outline-offset: 2px;
        }

        /* ensure anchor sections account for fixed header+nav */
        main section[id] {
            scroll-margin-top: var(--anchor-offset);
        }

        /* subtle blur for fixed bars on supported browsers */
        .app-header,
        .category-nav,
        .cart-bar {
            -webkit-backdrop-filter: saturate(1.2) blur(8px);
            backdrop-filter: saturate(1.2) blur(8px);
        }

        /* Safe-area support (iOS Safari toolbars) */
        .app-header {
            padding-top: max(env(safe-area-inset-top), 0px);
        }

        .cart-bar {
            padding-bottom: max(.25rem, env(safe-area-inset-bottom));
        }

        /* total text on checkout button */
        .btn-checkout .cart-amt {
            color: #0b1220;
            font-weight: 700;
        }

        /* grid rules no longer needed for list layout */

        /* make container full bleed on very small screens */
        @media (max-width: 420px) {
            main.container {
                padding-left: .5rem !important;
                padding-right: .5rem !important;
            }

            .cart-panel .btn-checkout {
                min-width: 128px;
                font-weight: 600;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="app-header">
        <div class="container py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="brand">
                    <img src="{{ asset('logo/sb-logo.png') }}" alt="Logo SB" class="brand-badge">
                    <div>
                        <div class="fw-bold" style="letter-spacing:.2px;font-size:1.05rem">SB Menu</div>
                        <div class="text-secondary" style="font-size:.8rem">Pesan cepat, tanpa antre</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Category Nav -->
    <div class="category-nav">
        <div class="container py-2">
            <div class="d-flex gap-2">
                @foreach ($mappedData ?? [] as $cat)
                    <a href="#cat-{{ $cat['id'] }}"
                        class="category-pill text-decoration-none">{{ $cat['name'] }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="container py-3">
        @foreach ($mappedData ?? [] as $cat)
            <section id="cat-{{ $cat['id'] }}" class="pt-2 pb-4">
                <h2 class="section-title mb-1">{{ $cat['name'] }}</h2>
                <div class="section-sub mb-3">{{ $cat['desc'] }}</div>

                <div class="vstack gap-2">
                    @foreach ($cat['items'] as $item)
                        <div class="product-row" data-product data-id="{{ $item['id'] }}"
                            data-name="{{ $item['name'] }}" data-price="{{ $item['price'] }}"
                            data-category="{{ $cat['name'] }}">
                            <img class="product-thumb" src="{{ $item['img'] }}" alt="{{ $item['name'] }}">
                            <div class="product-info flex-grow-1">
                                <div class="product-title">{{ $item['name'] }}</div>
                                <div class="product-sub">
                                    <div class="product-sub-left">
                                        <div class="product-meta text-secondary">{{ $cat['name'] }}</div>
                                        <div class="product-price fw-semibold">Rp
                                            {{ number_format($item['price'], 0, ',', '.') }}</div>
                                    </div>
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
                <div class="d-flex align-items-center gap-2 w-100">
                    <button class="btn btn-outline-light btn-icon" id="resetBtn" type="button" aria-label="Bersihkan">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M3 6h18M8 6l1-2h6l1 2M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="visually-hidden">Bersihkan</span>
                    </button>
                    <button
                        class="btn btn-checkout d-inline-flex align-items-center gap-2 px-3 flex-grow-1 justify-content-center"
                        id="checkoutBtn" type="button" disabled aria-label="Pesan">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M7 7h14l-1.5 9.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.5L5 4H2" stroke="#0b1220"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15 11.5l-3 3-1.5-1.5" stroke="#0b1220" stroke-width="1.6" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <strong id="cartTotal" class="cart-amt">Rp 0</strong>
                        <span class="visually-hidden">Pesan</span>
                    </button>
                    <span id="cartCount" class="visually-hidden">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#e6eef7">
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
                    <button type="button" class="btn btn-checkout" id="confirmBtn">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden order form (change action to your submit route) -->
    <form id="orderForm" method="POST" action="{{ route('createOrder', request('tableTransactionCode')) }}" class="d-none">
        @csrf
        <input type="hidden" name="cart" id="orderCart" />
        <input type="hidden" name="total" id="orderTotal" />
    </form>

    @include('backend.layout.partials.menu._foot')

    <script></script>

</body>

</html>
