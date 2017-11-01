<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Http\Controllers\Services\Locale\LocaleSettings;

class LocaleMiddleware
{
    const DEFAULT_LOCALE = LocaleSettings::createArr['en']['name'];
    const DEFAULT_LOCALE_ID = 1;

    public function handle($request, Closure $next)
    {
        $locale   = self::DEFAULT_LOCALE;
        $localeId = self::DEFAULT_LOCALE_ID;

        $requestedLocale = strtolower($request->segment(1));
        $localeSettings  = LocaleSettings::createArr;
        $existingLocales = array_keys($localeSettings);

        if (in_array($request->segment(1), $existingLocales)) {
            if ($requestedLocale !== $locale) {
                $locale = $requestedLocale;
                $localeId = $localeSettings[$locale]['id'];
                \App::setLocale($locale);
            }
        } else {
            return redirect('/' . self::DEFAULT_LOCALE);
        }

        Session::put('localeId', $localeId);

        return $next($request);
    }
}
