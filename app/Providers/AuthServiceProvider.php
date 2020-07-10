<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Gate::define('update-project', function ($user, $project) {
            if ($user->is_admin)
                return true;
            return $user->id === $project->user_id;
        });

        Gate::define('destroy-project', function ($user, $project) {
            if ($user->is_admin)
                return true;
            return $user->id === $project->user_id;
        });

        Gate::define('update-user', function ($user, $model) {
            if ($user->is_admin)
                return true;
            return $user->id === $model->id;
        });
    }
}
