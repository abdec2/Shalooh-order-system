<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <section class="text-gray-600 body-font relative">
                        <div class="container px-5 py-20 mx-auto">
                            <div class="flex flex-col  w-full mb-5">
                                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Reports</h1>
                            </div>
                            <form action="/reports" method="POST">
                                @csrf
                                <div class="lg:w-full">
                                    <div class="flex flex-wrap">
                                        <div class="p-2 w-full sm:w-1/3">
                                            <div class="relative">
                                                <input type="date" id="fromDate" name="fromDate"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                                    placeholder="Enter order number" required>
                                            </div>
                                        </div>
                                        <div class="p-2 w-full sm:w-1/3">
                                            <div class="relative">
                                                <input type="date" id="toDate" name="toDate"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                                    placeholder="Enter order number" required>
                                            </div>
                                        </div>
                                        <div class="p-2 w-full sm:w-1/3">
                                            <div class="relative">
                                                <button
                                                    class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-md w-full">Submit</button>
                                            </div>
                                        </div>
                                        @isset($type)
                                        @if($type == 'error')
                                        <div class="errormsg pl-2 text-sm text-red-500">
                                            <p>{{ $msg }}</p>
                                        </div>
                                            @else 
                                                <div class="errormsg pl-2 text-sm text-green-500">
                                                    <p>{{ $msg }}</p>
                                                </div>
                                        @endif
                                        @endisset
                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>

                    @isset($totalRecords)
                        <table id="report" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                            <thead>
                                <tr>
                                    <th data-priority="1">Order #</th>
                                    <th data-priority="2">Date</th>
                                    <th data-priority="3">Name</th>
                                    <th data-priority="4">Email</th>
                                    <th data-priority="5">Phone</th>
                                    <th data-priority="6">Shipping Method</th>
                                    <th data-priority="7">Payment Method</th>
                                    <th data-priority="8">Taxable Amount</th>
                                    <th data-priority="9">Tax Amount</th>
                                    <th data-priority="10">Grand Total</th>
                                    <th data-priority="11">Order Status</th>
                                </tr>
                            </thead>
                            <tbody>
                        @foreach($totalRecords as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->date_created }}</td>
                                    <td>{{ $order->billing->first_name.' '.$order->billing->last_name }}</td>
                                    <td>{{ $order->billing->email }}</td>
                                    <td>{{ $order->billing->phone }}</td>
                                    <td>{{ $order->shipping_lines[0]->method_title }}</td>
                                    <td>{{ $order->payment_method_title }}</td>
                                    <td>{{ (float)$order->total - (float)$order->total_tax - (float)$order->shipping_total }}</td>
                                    <td>{{ $order->total_tax }}</td>
                                    <td>{{ $order->total }}</td>
                                    <td>{{ $order->status }}</td>
                                </tr>
                                
                        @endforeach
                            </tbody>
                            
                        </table>

                        
                        
                    @endisset
                </div>
            </div>
        </div>
    </div>

</x-app-layout>