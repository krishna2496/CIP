<?php
namespace App\Http\Middleware;

use Closure;
use App\Helpers\LanguageHelper;

class LocalizationMiddleware
{
    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new localization middleware instance.
     *
     * @param App\Helpers\LanguageHelper $languageHelper
     * @return void
     */
    public function __construct(LanguageHelper $languageHelper)
    {
        $this->languageHelper = $languageHelper;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param object $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set localization to config locale
        config(['app.locale' => $request->header('X-localization')]);

        // Get tenant language base on localization or default language of tenant from database
        $language = $this->languageHelper->checkTenantLanguage($request);
        
        // set laravel localization
        app('translator')->setLocale($language->code);
        config(['app.locale' => $language->code]);
        
        // continue request
        return $next($request);
    }
}
