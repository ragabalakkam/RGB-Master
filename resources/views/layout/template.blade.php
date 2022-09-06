<?php

$SEO = [
    'Author'        => 'Afaq Al-Khaleej Organization for Information Technology',
    'Keywords'      => 'afaq,al-khaleej,khalej,store,online,online store,POS,point of sale,sales,purchases,buy,sell,sale,reports,warehouse,warehouses,products,recipes,ingredients,takeaway,local,dine in,dining,delivery,customer,customers,supplier,suppliers,movements,accounting,accounts,guides,',
    'Description'   => 'Afaq Store is a the product of Afaq Al-Khaleej Org. that provides store owners and stakeholders with the appropriate solutions',
];

$SEO_html = '';
foreach ($SEO as $name => $content) {
    $SEO_html .= "<meta name='$name' content='$content'>";
}

?>

<!DOCTYPE html>
<html>

<head>
    {{-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" /> --}}
    
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Author" content="Afaq Al-Khaleej Organization for Information Technology">
    <meta name="Keywords" content="afaq,al-khaleej,khalej,store,online,online store,POS,point of sale,sales,purchases,buy,sell,sale,reports,warehouse,warehouses,products,recipes,ingredients,takeaway,local,dine in,dining,delivery,customer,customers,supplier,suppliers,movements,accounting,accounts,guides," />
    <meta name="Description" content="Afaq Store is a the product of Afaq Al-Khaleej Org. that provides store owners and stakeholders with the appropriate solutions">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @yield('meta')

    <title> {{ config('app.name') }} </title>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css?t=') . time() }}" id="theme-stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print_vars.css?t=') . time() }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
</head>

<body class="vh-100">
    <div id="app">
        @yield('body')
    </div>

    {{-- Scripts --}}
    <script src="{{ asset("js/$js.js") }}"></script>
    <script src="{{ asset("js/vendor.js") }}" defer></script>
    <script src="{{ asset("js/manifest.js") }}" defer></script>
    @yield('js')
</body>

</html>
