<?php

namespace App\Livewire\BigCApp;

use App\Models\ConversionSetting;
use Livewire\Component;

class FacebookSettings extends Component
{
    public $pixel_id;
    public $access_token;

    public function mount()
    {
        $setting = ConversionSetting::where('store_id', session('store_id'))
            ->where('platform', 'facebook')->first();

        $this->pixel_id = $setting->settings['pixel_id'] ?? '';
        $this->access_token = $setting->settings['access_token'] ?? '';
    }

    public function save()
    {
        ConversionSetting::updateOrCreate(
            ['store_id' => session('store_id'), 'platform' => 'facebook'],
            ['settings' => [
                'pixel_id' => $this->pixel_id,
                'access_token' => $this->access_token,
            ]]
        );

        session()->flash('success', 'Facebook settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.big-c-app.facebook-settings');
    }
}
