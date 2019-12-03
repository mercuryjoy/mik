<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Admin;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('show-debug-features', function (Admin $admin) {
            return $admin->isSuperAdmin();
        });

        $gate->define('list-admin', function (Admin $admin) {
            return $admin->isSeniorAdmin();
        });

        $gate->define('list-normal', function (Admin $admin) {
            return $admin->isNormalAdmin();
        });

        $gate->define('send-red-envelope', function (Admin $admin) {
            return $admin->isSeniorAdmin();
        });

        $gate->define('edit-admin', function (Admin $admin, Admin $anotherAdmin) {
            return $admin->isSeniorAdmin() || $admin->id == $anotherAdmin->id;
        });
        $gate->define('destroy-admin', function (Admin $admin, Admin $anotherAdmin) {
            return ($admin->isSuperAdmin() || $admin->attributes['level'] >= $anotherAdmin->attributes['level']) && ($admin->id != $anotherAdmin->id);
        });
    }
}
