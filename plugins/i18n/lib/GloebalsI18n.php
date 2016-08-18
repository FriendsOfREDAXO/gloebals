<?php

class GloebalsI18n extends Gloebals
{
    protected static $plugin = 'i18n';

    public static function addTranslations($lang = null)
    {
        if($code = self::getLocaleCode($lang))
        {
            if(!isset(static::$locales[$code]))
            {
                static::$locales[$code] = true;

                if (
                    ($content = static::getSettings('strings_' . $code)) &&
                    preg_match_all("/^([^\s]*)\s*=\s*(.*\S)?\s*$/m", $content, $matches, PREG_SET_ORDER)
                ) {
                    foreach ($matches as $match) {
                        rex_i18n::addMsg($match[1], $match[2]);
                    }
                }
            }
        }
    }
}
