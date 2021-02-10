<x-app-layout>
    <x-slot name="header">

    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <section class="text-gray-600 body-font relative">
                        <div class="container px-5 py-20 mx-auto">
                            <div class="flex flex-col text-center w-full mb-5">
                                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">Orders</h1>
                            </div>
                            <form action="/orders" method="POST">
                                @csrf
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="p-2 w-1/2">
                                            <div class="relative">
                                                <input type="number" id="orderNumber" name="orderNumber"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                                    placeholder="Enter order number" required>
                                            </div>
                                        </div>
                                        <div class="p-2 w-1/2 ">
                                            <div class="relative">
                                                <button
                                                    class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-md">Search</button>
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
                    @isset($Order_ID)

                    <div class="mt-10 sm:mt-0">
                        <div class="md:grid md:grid-cols-5 md:gap-6">
                            <div class="md:col-span-1">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Order Information</h3>

                                </div>
                            </div>
                            <div class="mt-5 md:mt-0 md:col-span-3">
                                <form id="orderForm" action="/save_order" method="POST">
                                    @csrf
                                    <div class="shadow overflow-hidden sm:rounded-md">
                                        <div class="px-4 py-5 bg-white sm:p-6">
                                            <div class="grid grid-cols-6 gap-6">
                                                <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                                                    <label for="orderID"
                                                        class="block text-sm font-medium text-gray-700">Order ID</label>
                                                    <input type="number" name="orderID" id="orderID"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        readonly value="{{ $Order_ID }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                    <label for="fname"
                                                        class="block text-sm font-medium text-gray-700">First
                                                        Name</label>
                                                    <input type="text" name="fname" id="fname"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        readonly value="{{ $first_name }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                    <label for="lname"
                                                        class="block text-sm font-medium text-gray-700">Last
                                                        Name</label>
                                                    <input type="text" name="lname" id="lname"
                                                        autocomplete="postal-code"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        readonly value="{{ $last_name }}">
                                                </div>
                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="shippingMethod"
                                                        class="block text-sm font-medium text-gray-700">Shipping
                                                        Method</label>
                                                    <input type="text" name="shippingMethod" id="shippingMethod"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $shipping_method }}" readonly>
                                                </div>

                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="orderStatus"
                                                        class="block text-sm font-medium text-gray-700">Order
                                                        Status</label>
                                                    <select id="orderStatus" name="orderStatus"
                                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-yellow-600 focus:border-yellow-600 sm:text-sm"
                                                        required>
                                                        <option value="">Select</option>
                                                        @foreach($statusOpts as $key=>$statusOpt)
                                                        <option value="{{ $key }}" @if($key==$status)
                                                            selected @endif>{{ucfirst($statusOpt)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-span-6">
                                                    <label for="statusChangeReason"
                                                        class="block text-sm font-medium text-gray-700">Reason for
                                                        changing status</label>
                                                    <input type="text" name="statusChangeReason" id="statusChangeReason"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        @isset($statusChangeReason) value="{{ $statusChangeReason }}"
                                                        @endisset>
                                                </div>

                                                <div class="col-span-6">
                                                    <label for="shipping_address1"
                                                        class="block text-sm font-medium text-gray-700">Shipping Address
                                                        1</label>
                                                    <input type="text" name="shipping_address1" id="shipping_address1"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $shipping_address1 }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="shipping_address2"
                                                        class="block text-sm font-medium text-gray-700">Shipping Address
                                                        2</label>
                                                    <input type="text" name="shipping_address2" id="shipping_address2"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $shipping_address2 }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="contactNo"
                                                        class="block text-sm font-medium text-gray-700">Contact
                                                        No</label>
                                                    <input type="number" name="contactNo" id="contactNo"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $phone }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                                                    <label for="city"
                                                        class="block text-sm font-medium text-gray-700">City</label>
                                                    <input type="text" name="city" id="city"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $city }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                    <label for="state"
                                                        class="block text-sm font-medium text-gray-700">State /
                                                        Province</label>
                                                    <input type="text" name="state" id="state"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $state }}">
                                                </div>

                                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                    <label for="postal_code"
                                                        class="block text-sm font-medium text-gray-700">ZIP /
                                                        Postal</label>
                                                    <input type="text" name="postal_code" id="postal_code"
                                                        autocomplete="postal-code"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $postcode }}">
                                                </div>


                                                <div class="col-span-6">
                                                    <label for="shipping_country"
                                                        class="block text-sm font-medium text-gray-700">Country</label>
                                                    <select id="shipping_country" name="shipping_country"
                                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-yellow-600 focus:border-yellow-600 sm:text-sm"
                                                        required>
                                                        @foreach($countryOpt as $key => $Opt)
                                                        <option value="{{ $Opt }}" @if($countryOpt==$country)
                                                            selected @endif>{{ucfirst($key)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="payment_method"
                                                        class="block text-sm font-medium text-gray-700">Payment
                                                        Method</label>
                                                    <input type="text" name="payment_method" id="payment_method"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        value="{{ $payment_method }}" readonly>
                                                </div>
                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="trackingNo"
                                                        class="block text-sm font-medium text-gray-700">Tracking
                                                        No.</label>
                                                    <input type="text" name="trackingNo" id="trackingNo"
                                                        class="mt-1 focus:ring-yellow-600 focus:border-yellow-600 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                        @isset($trackingNo) value="{{ $trackingNo }}" @endisset
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                            <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Save
                                            </button>
                                            <button type="button"
                                                id="btnCreateLbl" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Create Label and Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:block" aria-hidden="true">
                        <div class="py-5">
                            <div class="border-t border-gray-200"></div>
                        </div>
                    </div>
                    @endisset
                </div>

            </div>
        </div>
    </div>
    
</x-app-layout>