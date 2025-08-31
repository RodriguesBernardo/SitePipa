<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Game;
use App\Models\News;
use App\Policies\GamePolicy;
use App\Policies\NewsPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        News::class => NewsPolicy::class,
        Game::class => GamePolicy::class,
        // Outras políticas podem ser adicionadas aqui
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}