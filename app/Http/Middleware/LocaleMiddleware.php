<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Http\Controllers\Services\Locale\LocaleSettings;

class LocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        $locale   = LocaleSettings::getDefaultLocale();
        $localeId = LocaleSettings::getDefaultLocaleID();

        $requestedLocale = strtolower($request->segment(1));
        $localeSettings  = LocaleSettings::createArr;
        $existingLocales = array_keys($localeSettings);

        if ($requestedLocale && in_array($requestedLocale, $existingLocales)) {
            if ($requestedLocale !== $locale) {
                $locale = $requestedLocale;
                $localeId = $localeSettings[$locale]['id'];
            }
            \App::setLocale($locale);
        } else {
            if ($requestedLocale === 'login' || $requestedLocale === 'logout' || $requestedLocale === 'qwentin') {
                return redirect('/' . $locale . '/' . $requestedLocale);
            }
            return redirect('/' . $locale);
        }

        Session::put('localeId', $localeId);

        return $next($request);
    }
}
