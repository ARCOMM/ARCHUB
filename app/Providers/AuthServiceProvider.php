<?php

namespace App\Providers;

use App\RoleEnum;
use App\Models\Portal\User;
use App\Models\Missions\Mission;

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

        Gate::before(function (User $user, $ability) {
            if ($user->hasARole(RoleEnum::ADMIN)) {
                return true;
            }
        });

        Gate::define('access-hub', function (User $user) {
            return $user->hasARole(RoleEnum::MEMBER, RoleEnum::RECRUIT, RoleEnum::RETIRED);
        });

        Gate::define('test-missions', function (User $user) {
            // Includes adding notes, downloading missions,
            // reading locked briefings, and seeing unverified missions
            return $user->hasARole(RoleEnum::TESTER);
        });

        Gate::define('test-mission', function (User $user, Mission $mission) {
            return $mission->isMine() || $user->hasARole(RoleEnum::TESTER);
        });

        Gate::define('verify-missions', function (User $user) {
            return $user->hasARole(RoleEnum::SENIOR_TESTER);
        });

        Gate::define('manage-mission', function (User $user, Mission $mission) {
            // Includes updating missions and locking briefings
            return $mission->isMine() || $user->hasARole(RoleEnum::SENIOR_TESTER);
        });

        Gate::define('deploy-missions', function (User $user) {
            return $user->hasARole(RoleEnum::SENIOR_TESTER);
        });

        Gate::define('delete-mission', function (User $user, Mission $mission) {
            return $user->hasARole(RoleEnum::OPERATIONS) || ($mission->isMine() && !($mission->existsInOperation() || $mission->hasBeenPlayed()));
        });

        Gate::define('share-missions', function (User $user) {
            return $user->hasARole(RoleEnum::STAFF);
        });

        Gate::define('manage-operations', function (User $user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('view-users', function (User $user) {
            return $user->hasARole(RoleEnum::STAFF);
        });

        Gate::define('view-applications', function (User $user) {
            return $user->hasARole(RoleEnum::STAFF);
        });

        Gate::define('manage-applications', function (User $user) {
            return $user->hasARole(RoleEnum::RECRUITER);
        });

        Gate::define('delete-media', function (User $user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('manage-tags', function (User $user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('set-maintainer', function (User $user) {
            return $user->hasARole(RoleEnum::OPERATIONS);
        });

        Gate::define('manage-public-media', function (User $user) {
            // Admins only
        });
    }
}
