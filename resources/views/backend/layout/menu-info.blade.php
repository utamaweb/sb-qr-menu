<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiosk Info</title>

    @include('backend.layout.partials.menu._head')
    <style>
        /* Glass effect card for centered info */
        .info-glass {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            background: rgba(15, 23, 42, 0.5);
            /* semi-dark with transparency */
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px) saturate(140%);
            -webkit-backdrop-filter: blur(10px) saturate(140%);
        }

        .info-logo {
            width: 96px;
            height: auto;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.25));
        }

        .info-glass .section-title {
            margin-bottom: .5rem;
        }
    </style>
</head>

<body style="padding-top: 0; padding-bottom: 0;">
    {{-- Layout info/redirect untuk kasus: kode meja tidak ada, sesi berakhir, dsb. --}}

    <!-- Centered Info Section -->
    <main class="container d-flex flex-column align-items-center justify-content-center text-center"
        style="min-height:70vh">

        <section class="info-glass text-center">
            <img src="{{ asset('logo/sb-logo.png') }}" alt="Logo" class="info-logo mb-3">
            <h3 class="section-title mb-2">{{ $infoTitle }}</h3>
            <h6 class="text-secondary fw-normal">{{ $infoSubtitle }}</h6>
        </section>
    </main>

    @include('backend.layout.partials.menu._foot')

</body>

</html>
