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
                                        <h1>Add Products</h2>
                                    </div>

                                    <form action="/wms/products/add_products" method="POST">
                                        @csrf
                                        <div class="mx-4 my-2 p-4">
                                            <div class="flex flex-wrap -m-2">
                                                <div class="p-2 w-full md:w-2/3">
                                                    <div class="relative">
                                                        <input type="file" id="csvUpload" name="csvUpload"
                                                            class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                                                            placeholder="Enter order number" required>
                                                    </div>
                                                </div>
                                                <div class="p-2 w-full md:w-1/3 ">
                                                    <div class="relative">
                                                        <button
                                                            class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none leading-8 hover:bg-yellow-600 rounded text-md w-full ">Upload</button>
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
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>



</x-app-layout>