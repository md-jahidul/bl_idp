<?php

namespace App\Providers;

use App\Auth\Grants\OtpGrant;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use League\OAuth2\Server\AuthorizationServer;

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
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        app(AuthorizationServer::class)->enableGrantType(
            $this->makeOtpGrant(), Passport::tokensExpireIn()
        );

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(5));

        Passport::refreshTokensExpireIn(now()->addDays(15));

        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Passport::enableImplicitGrant();
    }


    protected function makeOtpGrant()
    {
        $grant = new OtpGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
