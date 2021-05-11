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
                                        <h1 class="uppercase">Pending Orders</h2>
                                    </div>
                                    @if(count($orders) > 0)
                                        <div class="mx-4 my-2 p-4 ">
                                            <div class="mb-2">
                                                <x-dropdown align="left">
                                                    <x-slot name="trigger">
                                                        <button
                                                            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                                            <div>Action</div>

                                                            <div class="ml-1">
                                                                <svg class="fill-current h-4 w-4"
                                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                        </button>
                                                    </x-slot>

                                                    <x-slot name="content">
                                                        <x-dropdown-link onclick="waveOrder();">
                                                            {{ __('Wave') }}
                                                        </x-dropdown-link>
                                                        <script>
                                                            function waveOrder(){
                                                                let loading = document.querySelector('.loading');
                                                                let input = document.querySelectorAll('input[type=checkbox]:checked');
                                                                if(input.length === 0) {
                                                                    alertify.alert('You must check at least one Order to process');
                                                                    return;
                                                                }
                                                                let valArray = [];
                                                                input.forEach(item=>{
                                                                    valArray.push(item.value);
                                                                }) 
                                                                loading.style.display = 'block';
                                                                let formData = new FormData();
                                                                formData.append('order_id', JSON.stringify(valArray));
                                                                formData.append('_token', "{{ csrf_token() }}");
                                                                
                                                                fetch('/ab-ajax/wavaOrder', {
                                                                    method: 'POST', 
                                                                    body: formData
                                                                }).then(res=>res.json()).then(result=>{
                                                                    loading.style.display = 'none';
                                                                    if(result.type == 'success')
                                                                    {
                                                                        window.location.href='{{ route("wms.orders.processing") }}';
                                                                    }
                                                                }).catch(e=>{
                                                                    console.log(e);
                                                                    loading.style.display = 'none';
                                                                });
                                                            }
                                                        </script>
                                                    </x-slot>
                                                </x-dropdown>
                                            </div>
                                            <table class="table-fixed tblOrders">
                                                <thead>
                                                    <tr class="border-b-2">
                                                        <th class="w-1/12 p-2 ">S.No</th>
                                                        <th class="w-1/12 p-2">Order #</th>
                                                        <th class="w-1/4 p-2">Order Date</th>
                                                        <th class="w-1/6 p-2">Shipping Carrier</th>
                                                        <th class="w-1/6 p-2">Payment Method</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orders as $order)
                                                    <tr class="border-b-2">
                                                        <td class="p-2 text-center " align="center">
                                                            <input type="checkbox" id="order_id_{{ $order->id }}" name="order_id"
                                                                value="{{ $order->id }}" /></td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->order_number }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->order_date }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->shipping_carrier }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->payment_method }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{ $orders->links() }}
                                        </div>
                                    @else
                                    <div class="mx-4 my-2 p-4 text-sm">
                                        <p>Nothing Found. Sorry.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>