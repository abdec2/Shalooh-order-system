<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- <div class="p-6">
                <h1 class="text-2xl font-black antialiased">New Orders Received</h1>
            </div>

            <div class="newOrders">
                @foreach ($orders as $order)
                <div class="mt-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 bg-white border-b border-gray-200 ">
                        <div class="flex flex-col md:flex-row justify-between">
                            <div class="text-center p-2"><span class="font-bold">Order ID:</span> {{ $order['order_number'] }}</div>
                            <div class="text-center p-2"><span class="font-bold">Shipping Method:</span> {{ $order['shipping_method'] }}</div>
                            <div class="text-center p-2"><span class="font-bold">Order Status:</span> {{ $order['status'] }}</div>
                            <div class="text-center p-2"><a href="/orders/{{ $order['order_number'] }}" class="text-white bg-yellow-500 border-0 py-2 px-4 focus:outline-none hover:bg-yellow-600 rounded text-sm">View Order</a></div>
                        </div> 
                    </div>
                </div>
                @endforeach

            </div> -->

        </div>
    </div>
</x-app-layout>