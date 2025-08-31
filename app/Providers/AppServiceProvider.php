<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\HelpContent;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $helpContent = HelpContent::first();
            $view->with('globalHelpContent', $helpContent);
        });
    }
}