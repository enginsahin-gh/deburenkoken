<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        CustomException::class,
        TokenMismatchException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Mapping van POST routes naar hun bijbehorende GET pagina's.
     * Dit voorkomt MethodNotAllowedHttpException bij redirect na CSRF mismatch.
     *
     * @var array<string, string>
     */
    protected array $postToGetRouteMap = [
        // Contact form
        'contact-form' => 'contact',

        // Login & Register
        'login/submit' => 'login',
        'register/submit' => 'register/now',
        'register/verification' => 'register/now',
        'login/forgot/submit' => 'login/forgot',
        'login/forgot/reset/submit' => 'login/forgot/reset',

        // Order cancel (customer via email link) - pattern met parameters
        'order/cancel/{uuid}/{key}' => 'order/cancel/{uuid}',

        // Dashboard order cancel (cook)
        'dashboard/orders/cancel/{uuid}' => 'dashboard/orders/cancel/{uuid}',

        // Dashboard advert cancel
        'dashboard/adverts/cancel/{uuid}' => 'dashboard/adverts',

        // Cookie consent
        'accept-cookies' => '/',

        // Customer mailing list subscribe
        'customer/cook/subscribe/{uuid}' => '/',

        // Settings profile image
        'dashboard/settings/profile/image' => 'dashboard/settings',
        'dashboard/settings/details' => 'dashboard/settings/details',
        'dashboard/settings/details/location/update' => 'dashboard/settings/details/location',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Handle CSRF token mismatch (419 Page Expired) gracefully
        if ($e instanceof TokenMismatchException) {
            return $this->handleTokenMismatch($request);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->shouldReturnJson($request, $e)
                ? response()->json(['message' => 'not found.'], 404)
                : abort(404);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle CSRF token mismatch door gebruiker terug te sturen naar formulier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function handleTokenMismatch($request)
    {
        // Voor AJAX requests, return JSON response met nieuw CSRF token
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Je sessie is verlopen. Ververs de pagina en probeer het opnieuw.',
                'csrf_token' => csrf_token(),
            ], 419);
        }

        // Regenereer de sessie voor een nieuw CSRF token
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // Bepaal de juiste redirect URL
        $redirectUrl = $this->determineRedirectUrl($request);

        return redirect($redirectUrl)
            ->withInput($request->except($this->dontFlash))
            ->withErrors(['csrf' => 'Je sessie is verlopen. Het formulier is vernieuwd, probeer het opnieuw.']);
    }

    /**
     * Bepaal de juiste redirect URL na een CSRF mismatch.
     * Voorkomt redirect naar POST-only routes.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    protected function determineRedirectUrl($request): string
    {
        // Probeer eerst de referer te gebruiken (waar de gebruiker vandaan kwam)
        $referer = $request->headers->get('referer');

        if ($referer) {
            $refererPath = parse_url($referer, PHP_URL_PATH);
            if ($refererPath && $refererPath !== $request->getPathInfo()) {
                // Referer is een andere pagina, gebruik deze
                return $referer;
            }
        }

        // Check of we een mapping hebben voor deze POST route
        $currentPath = ltrim($request->getPathInfo(), '/');

        // Check exacte match
        if (isset($this->postToGetRouteMap[$currentPath])) {
            return url($this->postToGetRouteMap[$currentPath]);
        }

        // Check pattern match (voor routes met parameters zoals order/cancel/{uuid}/{key})
        foreach ($this->postToGetRouteMap as $postPattern => $getPath) {
            if (str_contains($postPattern, '{')) {
                // Bouw een regex pattern die de parameter waarden captured
                $namedPattern = preg_replace_callback(
                    '/\{([^}]+)\}/',
                    fn ($matches) => '(?P<'.$matches[1].'>[^/]+)',
                    $postPattern
                );

                if (preg_match('#^'.$namedPattern.'$#', $currentPath, $matches)) {
                    // Vervang placeholders in de GET path met werkelijke waarden
                    $resolvedPath = preg_replace_callback(
                        '/\{([^}]+)\}/',
                        fn ($m) => $matches[$m[1]] ?? $m[0],
                        $getPath
                    );

                    return url($resolvedPath);
                }
            }
        }

        // Fallback: probeer de route naam te vinden en naar een gerelateerde GET route te gaan
        $route = $request->route();
        if ($route) {
            $routeName = $route->getName();
            if ($routeName) {
                // Probeer een .show, .index, of basis route te vinden
                $baseName = preg_replace('/\.(submit|store|update|destroy)$/', '', $routeName);
                foreach (['.show', '.index', '.home', ''] as $suffix) {
                    $targetRoute = $baseName.$suffix;
                    if (\Route::has($targetRoute)) {
                        try {
                            return route($targetRoute);
                        } catch (\Exception $e) {
                            // Route heeft parameters die we niet hebben, ga door
                        }
                    }
                }
            }
        }

        // Laatste fallback: home pagina
        return route('home');
    }
}
