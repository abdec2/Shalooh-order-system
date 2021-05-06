<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pick N Pack') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <section class="text-gray-600 body-font relative">
                        <div class="container px-5 py-20 mx-auto">
                            <div class="pendingWork">
                                <div class="heading"><h1 class="text-2xl ">Pending Work:</h1></div>
                                @if(count($data) > 0)
                                    <div class="mt-5">
                                        <x-dropdown align="left">
                                            <x-slot name="trigger">
                                                <button
                                                    class="flex items-center text-sm font-medium text-white focus:outline-none focus:text-white transition duration-150 ease-in-out bg-yellow-500 focus:bg-yellow-600 p-2 rounded">
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
                                                <x-dropdown-link id="btnStartPicking" >
                                                    {{ __('Pick N Pack') }}
                                                </x-dropdown-link>
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                
                                    <div class="mt-8">
                                        <div class="flex flex-wrap -m-4">
                                            @foreach($data as $item)
                                                <div class="p-4 md:w-1/3 w-full">
                                                    <div class="flex rounded-lg h-full bg-gray-100 p-8 flex-col">
                                                        <div class="flex items-center mb-3">
                                                            <div
                                                                class="w-10 h-10 mr-3 inline-flex items-center justify-center rounded-full bg-yellow-500 text-white flex-shrink-0 p-1">
                                                                
                                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                </svg>

                                                
                                                            </div>
                                                            <h2 class="text-gray-900 text-lg title-font font-medium">Order Number: {{ $item->order->order_number }}</h2>
                                                        </div>
                                                        
                                                        <div class="flex-grow">
                                                            <p class="leading-relaxed text-base break-words"><strong class="text-yellow-500">Shipping Address:</strong> {{ $item->order->shipping_address }}</p>
                                                            <p class="leading-relaxed text-base break-words"><strong class="text-yellow-500">Tray:</strong> {{ ($item->pick_tray == null) ? 'none' : $item->pick_tray }}</p>
                                                        </div>
                                                        <button class="mt-16 text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg" onClick="assignTray({{ $item->id }})">Assign Tray</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                @else 
                                    <div class="m-4"><p>Sorry nothing found..</p></div>
                                @endif
                            </div>
                        </div>  
                    </section>
                </div>  
            </div>
        </div>
    </div>
    
</x-app-layout>