<?php

class GloebalsLocaleCsv extends Gloebals
{
    protected static $plugin = 'localecsv';

    protected static $strings = '';

    public static function missing_translation(rex_extension_point $ep)
    {
        $key = $ep->getParam('key');

        $trans = call_user_func_array('static::translate', $ep->getParam('args'));

        if($trans != $key)
        {
            return $trans;
        }
        else if(preg_match('/[^a-z0-9_-]/', $key))
        {
            return $key;
        }
        return $ep->getSubject();
    }

    public static function translate()
    {
        $arguments = func_get_args();
        if(!count($arguments))
        {
            return '';
        }
        $string = array_shift($arguments);

        if(empty($string) || !is_string($string))
        {
            return '';
        }

        $isPlural = !(isset($arguments[0]) && (float) $arguments[0] == 1);
        $string = static::getTranslationString($string, $isPlural);

        if($trans = @vsprintf($string, $arguments))
        {
            $string = $trans;
        }
        else if($trans = @sprintf($string))
        {
            $string = $trans;
        }

        return $string;
    }

    public static function getTranslationString($string, $isPlural = false)
    {
        $messages = static::getTranslationStrings();

        $key = $string;
        if(isset($messages[$key]))
        {
            $string = (array) $messages[$key];

            if((bool) $isPlural && is_array($string) && isset($string[1]))
            {
                $string = $string[1];
            }
            else
            {
                $string = $string[0];
            }
        }
        else
        {
            // do nothing as no matching string was found
        }

        return $string;
    }

    public static function hasTranslationString($string)
    {
        if(empty($string) || !is_string($string))
        {
            return false;
        }
        return $string != static::getTranslationString($string);
    }

    protected static function getTranslationStrings($lang = null)
    {
        if($code = self::getLocaleCode($lang))
        {
            if(!isset(static::$locales[$code]))
            {
                static::$locales[$code] = static::parseCsv(static::getSettings('strings_' . $code));
            }

            return static::$locales[$code];
        }

        return '';
    }

    public static function parseCsv($csvdata, $ignoreCase = false)
    {
        $return = array();

        $csvdata = explode("\n", $csvdata); // split the lines....
        foreach($csvdata as $data)
        {
            $data = str_getcsv($data); // parse each line
            if(count($data)>1)
            {
                $key = array_shift($data);
                if((bool) $ignoreCase)
                {
                    $key = strtolower($key);
                }

                $return[$key] = $data;
                foreach($return[$key] as $i => $r)
                {
                    $return[$key][$i] = stripslashes($r);
                }
                unset($key, $i, $r);
            }
        }
        unset($csvdata, $data);

        return $return;
    }




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
