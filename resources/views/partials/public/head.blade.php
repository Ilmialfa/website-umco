<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brandShort }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="preload" href="/fonts/barlow-condensed-700.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/plus-jakarta-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
            font-family: 'Barlow Condensed';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url('/fonts/barlow-condensed-500.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Barlow Condensed';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('/fonts/barlow-condensed-600.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Barlow Condensed';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: url('/fonts/barlow-condensed-700.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-style: normal;
            font-weight: 200 800;
            font-display: swap;
            src: url('/fonts/plus-jakarta-sans-latin.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
        @font-face {
            font-family: 'Instrument Sans';
            font-style: normal;
            font-weight: 400 700;
            font-display: swap;
            src: url('/fonts/instrument-sans-latin.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
    </style>
    <link rel="stylesheet" href="/fonts/fonts-accent.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="/fonts/fonts-accent.css"></noscript>

    <script type="application/ld+json">{!! json_encode($jsonLdPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
