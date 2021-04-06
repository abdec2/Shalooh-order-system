<div class="overflow-hidden shadow-lg border-t-4 border-yellow-500 bg-white mb-4 rounded-b-lg rounded-t border-red-light w-full">
    <div class="px-6 py-4 mb-2 mt-4 mb-8">

        <div class="uppercase tracking-wide text-c2 mb-4">Products</div>
        <a @if (!request()->routeIs('wms.list_products')) href="{{ route('wms.list_products') }}" @endif>
            <div data-id="0" class="flex cursor-pointer border px-4 py-2 text-lg text-grey-darkest border-b-0 hover:bg-gray-200" @if (request()->routeIs('wms.list_products')) style="border-left: 4px solid #f59e0b !important;" @endif>
                <div class="pl-2">List Products</div>
            </div>
        </a>

        <a @if (!request()->routeIs('wms.add_products')) href="{{ route('wms.add_products') }}" @endif>
            <div data-id="1" class="flex cursor-pointer border px-4 py-2 text-lg text-grey-darkest border-b-0 hover:bg-gray-200" @if (request()->routeIs('wms.add_products')) style="border-left: 4px solid #f59e0b !important;" @endif>
                <div class="pl-2">Add Products</div>
            </div>
        </a>

        <div class="uppercase tracking-wide text-c2 mb-4 mt-8">Orders</div>
        <a @if (!request()->routeIs('wms.orders.pending')) href="{{ route('wms.orders.pending') }}" @endif>
            <div data-id="2" class="flex cursor-pointer border px-4 py-2 text-lg text-grey-darkest hover:bg-gray-200" @if (request()->routeIs('wms.orders.pending')) style="border-left: 4px solid #f59e0b !important;" @endif>
                <div class="pl-2">Pending</div>
            </div>
        </a>
        <a @if (!request()->routeIs('wms.orders.processing')) href="{{ route('wms.orders.processing') }}" @endif>
            <div data-id="3" class="flex cursor-pointer border px-4 py-2 text-lg text-grey-darkest hover:bg-gray-200" @if (request()->routeIs('wms.orders.processing')) style="border-left: 4px solid #f59e0b !important;" @endif>
                <div class="pl-2">Processing</div>
            </div>
        </a>
        <a @if (!request()->routeIs('wms.orders.shipped')) href="{{ route('wms.orders.shipped') }}" @endif>
            <div data-id="4" class="flex cursor-pointer border px-4 py-2 text-lg text-grey-darkest hover:bg-gray-200" @if (request()->routeIs('wms.orders.shipped')) style="border-left: 4px solid #f59e0b !important;" @endif>
                <div class="pl-2">Shipped</div>
            </div>
        </a>

        <div>
            <div class="uppercase tracking-wide text-c2 mb-4 mt-8">Locations</div>
            <a href="#">
                <div data-id="5" class="flex cursor-pointer border px-4 py-2 text-lg text-grey-darkest hover:bg-gray-200" >
                    <div class="pl-2">Add Locations</div>
                </div>
            </a>
        </div>
    </div>
</div>