<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Which roles may use each capability. Owner is granted everything via Gate::before.
     *
     * @var array<string, list<string>>
     */
    private const ABILITIES = [
        // Whole-business visibility: dashboard analytics, reports, team performance.
        'view-analytics' => [User::ROLE_MANAGER],
        // Master data & operations: products, inventory, suppliers, customers, expenses, e-commerce.
        'manage-catalog' => [User::ROLE_MANAGER],
        // Login accounts and roles.
        'manage-users' => [],
        // Record sales (sales role is additionally scoped to its own records in controllers).
        'record-sales' => [User::ROLE_MANAGER, User::ROLE_SALES],
        // See and edit every sale, not just your own.
        'manage-all-sales' => [User::ROLE_MANAGER],
        // Record production batches.
        'record-production' => [User::ROLE_MANAGER, User::ROLE_PRODUCTION],
        // See and edit every production batch.
        'manage-all-production' => [User::ROLE_MANAGER],
        // Delete financial/stock records (kept away from front-line staff).
        'delete-records' => [User::ROLE_MANAGER],
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix for older MySQL/MariaDB utf8mb4 index length limits.
        Schema::defaultStringLength(191);

        // Without this an already signed-in user who opens the login page lands on the
        // public storefront, which looks like a failed login. Send them to their own screen.
        RedirectIfAuthenticated::redirectUsing(function (Request $request): string {
            $user = $request->user();

            return $user ? route($user->homeRoute()) : route('storefront.home');
        });

        // Owner can do everything; inactive accounts can do nothing.
        Gate::before(function (User $user, string $ability) {
            if (! $user->is_active) {
                return false;
            }

            return $user->isOwner() ? true : null;
        });

        foreach (self::ABILITIES as $ability => $roles) {
            Gate::define($ability, fn (User $user): bool => $user->hasRole(...$roles));
        }

        // Share site contact data with every storefront view.
        // Wrapped in a try/catch so artisan commands during fresh installs don't crash.
        View::composer('storefront.*', function (\Illuminate\View\View $view): void {
            try {
                $svc = app(SiteSettingsService::class);
                $view->with([
                    'siteSettings'      => $svc->all(),
                    'sitePhones'        => $svc->channels('phone'),
                    'siteEmails'        => $svc->channels('email'),
                    'siteWhatsapps'     => $svc->channels('whatsapp'),
                    'siteAddresses'     => $svc->channels('address'),
                    'siteSocials'       => $svc->channels('social'),
                    'primaryPhone'      => $svc->primaryChannel('phone'),
                    'primaryEmail'      => $svc->primaryChannel('email'),
                    'primaryWhatsapp'   => $svc->primaryChannel('whatsapp'),
                    'primaryAddress'    => $svc->primaryChannel('address'),
                ]);
            } catch (\Throwable) {
                $view->with([
                    'siteSettings'    => [],
                    'sitePhones'      => collect(),
                    'siteEmails'      => collect(),
                    'siteWhatsapps'   => collect(),
                    'siteAddresses'   => collect(),
                    'siteSocials'     => collect(),
                    'primaryPhone'    => null,
                    'primaryEmail'    => null,
                    'primaryWhatsapp' => null,
                    'primaryAddress'  => null,
                ]);
            }
        });

        // Share with the storefront layout component too (footer lives there).
        View::composer('components.layouts.storefront', function (\Illuminate\View\View $view): void {
            try {
                $svc = app(SiteSettingsService::class);
                $view->with([
                    'siteSettings'    => $svc->all(),
                    'primaryPhone'    => $svc->primaryChannel('phone'),
                    'primaryEmail'    => $svc->primaryChannel('email'),
                    'primaryWhatsapp' => $svc->primaryChannel('whatsapp'),
                    'primaryAddress'  => $svc->primaryChannel('address'),
                    'siteSocials'     => $svc->channels('social'),
                ]);
            } catch (\Throwable) {
                $view->with([
                    'siteSettings'    => [],
                    'primaryPhone'    => null,
                    'primaryEmail'    => null,
                    'primaryWhatsapp' => null,
                    'primaryAddress'  => null,
                    'siteSocials'     => collect(),
                ]);
            }
        });
    }
}
