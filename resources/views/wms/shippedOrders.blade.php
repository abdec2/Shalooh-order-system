<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Warehouse Management System') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <section class="text-gray-600 body-font relative">
                        <div class="container px-5 py-20 mx-auto">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-3">
                                    <x-side-menu />
                                </div>
                                <div class="col-span-12 md:col-span-9 ">
                                    <div class="mx-4 my-2 p-4 text-2xl">
                                        <h1 class="uppercase">Shipped Orders</h2>
                                    </div>

                                    @if(count($orders) > 0)
                                    <div class="mx-4 my-2 p-4 ">
                                        <div class="overflow-x-auto" >
                                            <table class="table-fixed">
                                                <thead>
                                                    <tr class="border-b-2">
                                                        <th class="w-1/12 p-2 ">S.No</th>
                                                        <th class="w-1/12 p-2 ">Order #</th>
                                                        <th class="w-1/4 p-2 ">Order Date</th>
                                                        <th class="w-1/6 p-2 ">Shipping Carrier</th>
                                                        <th class="w-1/6 p-2 ">Shipping Method</th>
                                                        <th class="w-1/6 p-2 ">View Order</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orders as $order)
                                                    <tr class="border-b-2">
                                                        <td class="p-2 text-center " align="center">
                                                            <input type="checkbox" id="order_id_{{ $order->id }}"
                                                                name="order_id" value="{{ $order->id }}" />
                                                        </td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->order_number }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->order_date }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->shipping_carrier }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->payment_method }}</td>
                                                        <td class="p-2 text-center ">
                                                            <button class="w-full text-white bg-blue-500 hover:bg-blue-600 focus:outline-none rounded" onclick="viewOrder({{ $order->id }})">View</button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{ $orders->links() }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>