<?php

namespace App\View\Composers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingComposer
{
    public function compose(View $view): void
    {
        $settings = Cache::rememberForever('site_settings', fn () => Setting::allKeyed());

        $view->with('settings', $settings);
    }
}
