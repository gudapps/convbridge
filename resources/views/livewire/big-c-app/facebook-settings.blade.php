<div class="max-w-xl mx-auto mt-10 p-6 bg-white shadow rounded-xl">
    <h2 class="text-xl font-bold mb-4">Facebook Conversion API Settings</h2>

    @if (session()->has('success'))
        <div class="mb-4 text-green-700 font-semibold">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Pixel ID</label>
            <input wire:model="pixel_id" type="text" class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block text-sm font-medium">Access Token</label>
            <input wire:model="access_token" type="text" class="w-full border rounded p-2">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
    </form>

    <p class="mt-4">
        <a href="https://developers.facebook.com/docs/marketing-api/conversions-api/get-started" target="_blank">Click here for information to create PIXEL/Access token</a>
    </p>

    <hr class="mt-4 mb-4"/>

    <h2 class="text-xl font-semibold mt-6">Inject Facebook Pixel Scripts</h2>

    @if (empty($pixel_id))
        <p class="text-sm text-red-600 mb-2">Please add Pixel ID first</p>
    @endif

    <div class="space-y-6 mt-4">

        {{-- Main Script --}}
        <div>
            <p class="mb-2 text-gray-700">
                <strong>Main Pixel Script:</strong> Injects the core Facebook Pixel script which initializes your Pixel ID and tracks page views automatically.
            </p>
            <button wire:click="injectMainPixel"
                @disabled(empty($pixel_id))
                @disabled($scriptStatus['main'])
                style="{{ empty($pixel_id) | $scriptStatus['main'] ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;' }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                Inject Main Pixel Script
            </button>

            <button wire:click="deleteMainPixel" class="text-red-600 cursor-pointer text-sm">❌</button>
        </div>

        {{-- PageView --}}
        <div>
            <p class="mb-2 text-gray-700">
                <strong>PageView Event:</strong> Manually adds a PageView tracking call on all pages. Use only if you’re not using the main script.
            </p>
            <button wire:click="injectPageView"
                @disabled(empty($pixel_id))
                style="{{ empty($pixel_id) ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;' }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded border border-gray-300 transition">
                Inject PageView Event
            </button>
        </div>

        {{-- ViewContent --}}
        <div>
            <p class="mb-2 text-gray-700">
                <strong>ViewContent Event:</strong> Tracks when a visitor views a product detail page. Useful for retargeting interested customers.
            </p>
            <button wire:click="injectViewContent"
                @disabled(empty($pixel_id))
                style="{{ empty($pixel_id) ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;' }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded border border-gray-300 transition">
                Inject ViewContent Event
            </button>
        </div>

        {{-- AddToCart --}}
        <div>
            <p class="mb-2 text-gray-700">
                <strong>AddToCart Event:</strong> Tracks when a customer adds a product to their cart. Great for measuring buyer intent.
            </p>
            <button wire:click="injectAddToCart"
                @disabled(empty($pixel_id))
                style="{{ empty($pixel_id) ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;' }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded border border-gray-300 transition">
                Inject AddToCart Event
            </button>
        </div>

        {{-- Purchase --}}
        <div>
            <p class="mb-2 text-gray-700">
                <strong>Purchase Event:</strong> Captures completed orders to help Facebook optimize for conversions and return on ad spend.
            </p>
            <button wire:click="injectPurchase"
                @disabled(empty($pixel_id))
                style="{{ empty($pixel_id) ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;' }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded border border-gray-300 transition">
                Inject Purchase Event
            </button>
        </div>

    </div>
</div>
