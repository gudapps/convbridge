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

    <p>
        <a href="https://developers.facebook.com/docs/marketing-api/conversions-api/get-started" target="_blank">Click here for information to create PIXEL/Access token</a>
    </p>
</div>
