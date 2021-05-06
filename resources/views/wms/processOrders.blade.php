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
                                        <h1 class="uppercase">Processing Orders</h2>
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
                                                    <x-dropdown-link id="btnFulfillment" >
                                                        {{ __('Fulfillment') }}
                                                    </x-dropdown-link>
                                                    
                                                </x-slot>
                                            </x-dropdown>
                                        </div>
                                        <div id="boxOverlay"
                                            class="bg-black bg-opacity-50 fixed inset-0 hidden justify-center items-center flex">
                                            <div class="bg-gray-200 w-1/4 min-w-max py-2 px-3 rounded shadow-xl text-gray-800">
                                                <div class="flex justify-between items-center">
                                                    <div class="flex">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <h4 class="text-lg font-bold">Fulfillment</h4>
                                                    </div>
                                                    <svg id="close-btn"
                                                        class="w-6 h-6 cursor-pointer p-1 bg-gray-200 rounded-full hover:bg-gray-300"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                                <div class="mt-8 flex">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 pb-2" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                    </svg>
                                                    <h1 class="text-xl font-bold">Assign Picker</h1>
                                                </div>
                                                <form id="fulfillmentForm">
                                                    <div class="mt-2">
                                                        <select class="w-full rounded" name="picker" id="picker" required>
                                                            <option value="">Select...</option>
                                                            @foreach($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mt-3 flex justify-end space-x-3">
                                                        <input type="button" id="btnCancel"
                                                            class="px-3 py-1 rounded hover:bg-red-300 hover:bg-opacity-50 hover:text-red-900" value="Cancel" />
                                                        <input type="submit" id="btnSubmitBox"
                                                            class="px-3 py-1 bg-blue-500 text-gray-200 rounded hover:bg-blue-600" value="Process" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="overflow-x-auto" >
                                            <table class="table-fixed tblOrders">
                                                <thead>
                                                    <tr class="border-b-2">
                                                        <th class="w-1/12 p-2 ">S.No</th>
                                                        <th class="w-1/12 p-2 ">Order #</th>
                                                        <th class="w-1/4 p-2 ">Order Date</th>
                                                        <th class="w-1/6 p-2 ">Shipping Carrier</th>
                                                        <th class="w-1/6 p-2 ">Shipping Method</th>
                                                        <th class="w-1/6 p-2 ">Ship Order</th>
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
                                                            {{ $order->order_number }}</td>
                                                        <td class="p-2 text-center ">
                                                            {{ $order->order_number }}</td>
                                                        <td class="p-2 text-center ">
                                                            <button class="w-full text-white bg-blue-500 rounded ring-transparent border-transparent" onclick="shipOrder({{ $order->id }})">Ship</button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{ $orders->links() }}
                                        </div>
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