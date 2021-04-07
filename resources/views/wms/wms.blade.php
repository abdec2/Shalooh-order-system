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
                                        <h1 class="uppercase">List Products</h2>
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