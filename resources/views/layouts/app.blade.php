<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name', 'Shalooh Order System') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="loading">
            <img src="{{ asset('img/loader.gif') }}" alt="">
        </div>
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="{{ asset('js/script.js') }}"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {

                var table = $('#report').DataTable( {
                        dom: 'Bfrtip',
                        scrollX: true,
                        responsive: true,
                        buttons: [
                         'excel'
                        ],
                        

                    } )
                    .columns.adjust();

                if( document.querySelector('#city') !== undefined && document.querySelector('#city') !== null ){
                    $('#city').select2({
                        selectionCssClass: ':all:',
                    });

                    
                    document.querySelector('#shipping_country').addEventListener('change', e=>{
                        let shipping_country = document.querySelector('#shipping_country option:checked').value;
                        let _token = document.querySelector('input[name="_token"]').value;
                        
                        let data = new FormData();
                        data.append('shipping_country', shipping_country);
                        data.append('_token', _token);
                        fetch('orders/get_cities', {
                            method: 'POST',
                            body: data

                        }).then(res=>res.json()).then(result=>{
                            document.querySelector('#city').innerHTML = result;
                        }).catch(e=>console.log(e));
                    });
                }
            } );
        </script>
    </body>
</html>
