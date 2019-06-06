<?php

namespace App\Containers\Localization\Middlewares;

use App\Containers\Localization\Exceptions\UnsupportedLanguageException;
use App\Ship\Parents\Middlewares\Middleware;
use ArrayIterator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Class LocalizationMiddleware
 *
 * @author Mahmoud Zalt  <mahmoud@zalt.me>
 */
class LocalizationMiddleware extends Middleware
{

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return  mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // find and validate the lang on that request
        $lang = $this->validateLanguage($this->findLanguage($request));

        // set the local language
        App::setLocale($lang);

        // get the response after the request is done
        $response = $next($request);

        // set Content Languages header in the response
        $response->headers->set('Content-Language', $lang);

        // return the response
        return $response;
    }

    /**
     * @param $requestLanguages
     *
     * @return string
     * @throws UnsupportedLanguageException
     */
    private function validateLanguage($requestLanguages)
    {
        /*
         * be sure to check $lang of the format "de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4"
         * this means:
         *  1) give me de-DE if it is available
         *  2) otherwise, give me de
         *  3) otherwise, give me en-US
         *  4) if all fails, give me en
        */

        // split it up by ","
        $languages = explode(',', $requestLanguages);

        // we need an ArrayIterator because we will be extending the FOREACH below dynamically!
        $languageIterator = new ArrayIterator($languages);

        $supportedLanguages = $this->getSupportedLanguages();

        foreach ($languageIterator as $language) {
            // split it up by ";"
            $locale = explode(';', $language);

            $currentLocale = $locale[0];

            // now check, if this locale is "supported"
            if (array_search($currentLocale, $supportedLanguages) !== false) {
                return $currentLocale;
            }

            // now check, if the language to be checked is in the form of de-DE
            if (Str::contains($currentLocale, '-')) {
                // extract the "main" part ("de") and append it to the end of the languages to be checked
                $base = explode('-', $currentLocale);
                $languageIterator[] = $base[0];
            }
        }

        // we have not found any language that is supported
        throw new UnsupportedLanguageException();
    }

    /**
     * @param $request
     *
     * @return  string
     */
    private function findLanguage($request)
    {
        /*
         * read the accept-language from the request
         * if the header is missing, use the default local language
         */
        $language = Config::get('app.locale');

        if ($request->hasHeader('Accept-Language')) {
            $language = $request->header('Accept-Language');
        }

        return $language;
    }

    /**
     * @return array
     */
    private function getSupportedLanguages()
    {
        $supportedLocales = [];

        $locales = Config::get('localization-container.supported_languages');

        foreach ($locales as $key => $value) {
            // it is a "simple" language code (e.g., "en")!
            if (!is_array($value)) {
                $supportedLocales[] = $value;
            }

            // it is a combined language-code (e.g., "en-US")
            if (is_array($value)) {
                foreach ($value as $v) {
                    $supportedLocales[] = $v;
                }
                $supportedLocales[] = $key;
            }
        }

        return $supportedLocales;
    }
}
