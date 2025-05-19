<div class="max-w-3xl mx-auto mt-10 space-y-6">
    <h1 class="text-2xl font-bold text-center">Conversion Settings Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
        <a href="{{ route('settings.facebook') }}" class="block bg-white shadow rounded-xl p-6 hover:shadow-lg">
            <div class="flex justify-center items-center mb-2">
                <img src="https://convbridge.com/images/facebook_icon.png" alt="Facebook Conversion API (CAPI)" class="h-8">
            </div>
            <div class="font-semibold">Facebook Settings</div>
        </a>
        <a href="{{ route('settings.google') }}" class="block bg-white shadow rounded-xl p-6 hover:shadow-lg">
            <div class="flex justify-center items-center mb-2">
                <img src="{{ asset('images/google_ads_icon.png') }}" alt="Google Ads Conversion API" class="h-8">
            </div>
            <div class="font-semibold">Google Settings</div>
        </a>
        <a href="{{ route('settings.bing') }}" class="block bg-white shadow rounded-xl p-6 hover:shadow-lg">
            <div class="flex justify-center items-center mb-2">
                <img src="{{ asset('images/bing_icon.png') }}" alt="Bing Ads Conversion API" class="h-8">
            </div>
            <div class="font-semibold">Bing Settings</div>
        </a>
    </div>
</div>
