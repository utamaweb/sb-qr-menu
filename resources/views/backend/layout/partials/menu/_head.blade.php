<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Optional Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --brand: #ff6b00;
        --brand-600: #e65f00;
        --ink: #0f172a;
        --muted: #64748b;
        --surface: #0b1220;
        --card: #0f172a;
        --ring: rgba(255, 107, 0, .35);
        /* Simplified fixed heights so header & nav always visible */
        --header-height: 64px;
        --nav-height: 56px;
        --anchor-offset: calc(var(--header-height) + var(--nav-height) + 8px);
    }

    html,
    body {
        height: 100%;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
        background: linear-gradient(180deg, #0b1220 0%, #0a0f1a 100%);
        color: #e5edf5;
        /* Fallback space for sticky cart bar, will be synced by JS to exact height */
        padding-bottom: 110px;
        padding-top: 110px;
    }

    /* CSS-only smooth scrolling for anchor links */
    html {
        scroll-behavior: smooth;
    }

    /* Header */
    .app-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1040;
        backdrop-filter: saturate(160%) blur(10px);
        background: rgba(10, 15, 26, 0.6);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        transition: transform .25s ease;
        will-change: transform;
        min-height: var(--header-height);
        /* ensure stable space until JS sets --header-height */
    }

    /* No hide-on-scroll anymore */

    .brand {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .brand-badge {
        width: 40px;
        height: 40px;
        border-radius: 12px;
    }

    /* Category nav sits under header, both always visible */
    .category-nav {
        position: fixed;
        top: var(--header-height);
        left: 0;
        right: 0;
        z-index: 1041;
        background: rgba(10, 15, 26, .6);
        backdrop-filter: saturate(160%) blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, .06);
    }

    /* Add space for fixed header + nav */
    main {
        padding-top: calc(var(--header-height) + var(--nav-height) + 12px);
    }

    .category-pill {
        color: #dbe7f3;
        border: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .03);
        border-radius: 999px;
        padding: .5rem .9rem;
        font-weight: 600;
        font-size: .9rem;
        transition: all .2s ease;
    }

    .category-pill:hover {
        transform: translateY(-1px);
        border-color: rgba(255, 255, 255, .18);
    }

    .category-pill.active,
    .category-pill:focus {
        background: rgba(255, 107, 0, .16);
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--ring);
        color: #fff;
    }

    /* Cards */
    .product-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .product-card:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, .15);
        box-shadow: 0 12px 36px rgba(0, 0, 0, .35);
    }

    .product-media {
        position: relative;
        border-bottom: 1px solid rgba(255, 255, 255, .06);
    }

    .product-img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
    }

    .badge-price {
        position: absolute;
        right: .75rem;
        bottom: .75rem;
        font-weight: 700;
        background: rgba(10, 15, 26, .85);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .08);
        padding: .35rem .6rem;
        border-radius: .65rem;
        font-size: .85rem;
    }

    .product-title {
        font-weight: 700;
        letter-spacing: .2px;
        color: #f4f7fb;
    }

    .product-desc {
        color: #9fb1c7;
        font-size: .9rem;
    }

    .product-meta {
        color: #bcd;
        font-size: .85rem;
    }

    /* Qty control */
    .qty-wrap {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .qty-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .04);
        color: #fff;
        font-weight: 700;
        font-size: 1.1rem;
        display: grid;
        place-items: center;
        transition: all .2s ease;
    }

    .qty-btn:hover {
        background: rgba(255, 255, 255, .08);
        border-color: rgba(255, 255, 255, .18);
        transform: translateY(-1px);
    }

    .qty-btn.plus {
        background: linear-gradient(180deg, rgba(255, 107, 0, .25) 0%, rgba(255, 107, 0, .12) 100%);
        border-color: rgba(255, 107, 0, .4);
    }

    .qty-btn.plus:hover {
        background: linear-gradient(180deg, rgba(255, 107, 0, .35) 0%, rgba(255, 107, 0, .2) 100%);
    }

    .qty-num {
        min-width: 2rem;
        text-align: center;
        font-weight: 700;
        font-size: 1.05rem;
        color: #fff;
    }

    /* Sticky cart */
    .cart-bar {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1050;
        padding: .75rem;
        /* Account for iOS/Android safe area */
        padding-bottom: calc(.75rem + env(safe-area-inset-bottom, 0px));
        background: rgba(10, 15, 26, .8);
        backdrop-filter: blur(10px) saturate(150%);
        border-top: 1px solid rgba(255, 255, 255, .06);
    }

    .cart-panel {
        border: 1px solid rgba(255, 255, 255, .09);
        border-radius: 14px;
        padding: .75rem 1rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, .05) 0%, rgba(255, 255, 255, .03) 100%);
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .6rem;
        font-weight: 700;
        font-size: .85rem;
        color: #fff;
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .10);
        border-radius: 999px;
    }

    .btn-checkout {
        border-radius: 12px;
        border: 1px solid rgba(255, 107, 0, .5);
        background: linear-gradient(180deg, #ff7a1f 0%, #ff5a00 100%);
        color: #fff;
        font-weight: 800;
        letter-spacing: .2px;
        padding: .7rem 1rem;
        box-shadow: 0 10px 20px rgba(255, 107, 0, .35);
    }

    .btn-checkout:disabled {
        opacity: .6;
        filter: grayscale(.2);
        box-shadow: none;
    }

    /* Section heading */
    .section-title {
        color: #eaf2fb;
        font-weight: 800;
        letter-spacing: .2px;
    }

    .section-sub {
        color: #90a4bc;
    }

    /* Utilities */
    .muted {
        color: var(--muted);
    }

    .shadow-soft {
        box-shadow: 0 12px 30px rgba(0, 0, 0, .35);
    }

    .ring:focus {
        outline: none;
        box-shadow: 0 0 0 4px var(--ring);
    }

    /* Prevent anchor targets from being hidden behind header+nav */
    section[id^="cat-"] {
        scroll-margin-top: var(--anchor-offset);
    }

    /* Dynamic spacer to ensure bottom content isn't covered by sticky cart */
    .bottom-spacer {
        height: 120px;
    }
</style>
