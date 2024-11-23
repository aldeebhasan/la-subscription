<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>La Subscription</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <!-- Scripts -->
    {{ css() }}
    {{ js() }}

</head>

<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    <!-- Page Content -->
    <div class="flex flex-col sm:flex-row " id="app">
        <div class="basis-1/5 h-screen p-2 bg-gray-200 bg-clip-padding border-4 border-gray-400 border-dashed ">
            @include('la-subscription::navigation')
        </div>
        <div class="flex-grow h-screen sm:overflow-y-auto">
            <router-view></router-view>
        </div>


    </div>
</div>
</body>

</html>
