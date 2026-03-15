<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebsiteAccess
{
    /**
     * Routes die zijn uitgesloten van Basic Auth (voor webhooks van externe services).
     */
    protected array $excludedPaths = [
        'mollie/webhook',
        'mollie/webhook/*',
    ];

    public function handle($request, Closure $next)
    {
        // Skip Basic Auth voor lokale development, PHPUnit tests, en Dusk tests
        // Note: 'testing' is de PHPUnit omgeving (phpunit.xml), niet test.deburenkoken.nl
        // test.deburenkoken.nl gebruikt APP_ENV=local met WEBSITE_ACCESS credentials
        if (app()->environment('local', 'testing', 'dusk.testing')) {
            return $next($request);
        }

        $username = config('app.website_access_username');
        $password = config('app.website_access_password');

        // Skip Basic Auth als er geen credentials geconfigureerd zijn
        if (empty($username) || empty($password)) {
            return $next($request);
        }

        // Check of dit een uitgesloten pad is (bijv. Mollie webhooks)
        if ($this->isExcludedPath($request)) {
            return $next($request);
        }

        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            $headers = ['WWW-Authenticate' => 'Basic'];

            return response('Unauthorized', 401, $headers);
        }

        return $next($request);
    }

    /**
     * Controleer of het huidige pad is uitgesloten van Basic Auth.
     */
    protected function isExcludedPath(Request $request): bool
    {
        foreach ($this->excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
