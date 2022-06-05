<?php

namespace App\Providers;

use App\RoleEnum;

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

        Gate::before(function ($user, $ability) {
            if ($user->hasARole(RoleEnum::ADMIN)) {
                return true;
            }
        });

        Gate::define('access-hub', function ($user) {
            return $user->hasARole(RoleEnum::MEMBER, RoleEnum::RECRUIT, RoleEnum::RETIRED);
        });

        Gate::define('test-missions', function ($user) {
            // Includes adding notes, downloading missions,
            // read locked briefings, and see unverified missions
            return $user->hasARole(RoleEnum::TESTER);
        });

        Gate::define('verify-missions', function ($user) {
            return $user->hasARole(RoleEnum::SENIOR_TESTER);
        });

        Gate::define('manage-missions', function ($user) {
            // Includes updating missions and locking briefings
            return $user->hasARole(RoleEnum::SENIOR_TESTER);
        });

        Gate::define('delete-missions', function ($user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('share-missions', function ($user) {
            return $user->hasARole(RoleEnum::STAFF);
        });

        Gate::define('manage-operations', function ($user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('view-users', function ($user) {
            return $user->hasARole(RoleEnum::STAFF);
        });

        Gate::define('view-applications', function ($user) {
            return $user->hasARole(RoleEnum::STAFF);
        });

        Gate::define('manage-applications', function ($user) {
            return $user->hasARole(RoleEnum::RECRUITER);
        });

        Gate::define('delete-media', function ($user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('manage-tags', function ($user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('manage-public-media', function ($user) {
            // Admins only
        });
    }
}
