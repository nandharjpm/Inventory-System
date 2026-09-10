<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Shopping App')</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <header class="app-header">
            <div class="container header-content">
                <div class="brand">
                    <a href="{{ url('/') }}">Shopping App</a>
                </div>

                <nav class="navigation">
                    <a href="{{ route('orders.create') }}">New Order</a>
                    <a href="{{ route('orders.customers') }}">Customers</a>
                </nav>
            </div>
        </header>

        <main>
            <div class="container">
                @yield('content')
            </div>
        </main>

        @stack('scripts')
    </body>
</html>
