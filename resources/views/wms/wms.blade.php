<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Warehouse Management System') }}
            @if (session('status'))
                <p class="text-xs text-green-400">Products Added Successfully...</p>
            @endif
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
                                        <h1 class="uppercase">List Products</h2>
                                    </div>
                                    @if(count($products) > 0)
                                    <table id="tblListProduct" class="table-fixed ">
                                        <thead>
                                        <tr class="border-b-2">
                                            <th class="w-1/6 p-2">Image</th>
                                            <th class="w-1/2 text-left p-2">Label</th>
                                            <th class="w-1/6 p-2">SKU</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $product)
                                            <tr class="border-b-2">
                                                <td class="p-2"><img src="{{ $product->image_path }}" /></td>
                                                <td class="p-2">{{ $product->label }}</td>
                                                <td class="p-2 text-center">{{ $product->sku }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination my-8">
                                        {{ $products->links() }}
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