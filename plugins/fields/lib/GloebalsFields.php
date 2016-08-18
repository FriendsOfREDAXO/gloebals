<?php

class GloebalsFields extends Gloebals
{
    protected static $plugin = 'fields';

    public static function addFields($lang = null)
    {
        $config = [];

        // load default language
        $codes = [
            rex_clang::get(rex_clang::getStartId())->getCode(),
            rex_clang::getCurrent()->getCode()
        ];
        $codes = array_unique($codes);

        foreach($codes as $code)
        {
            if ($content = static::getSettings('fields_' . $code))
            {
                $content = preg_replace('/^ {2}/s', "\t", $content);
                try {
                    $yaml = rex_string::yamlDecode($content);
                } catch (\Exception $e) {
                    $yaml = [];
                    static::addError($e->getMessage());
                }

                $config = static::replace($config, $yaml);
            }
        }

        if(!empty($config))
        {
            rex_config::set(static::$addon, $config);
        }
    }

    protected static function replace(array $src, array $dst = [])
    {
        if(!empty($dst))
        {
            foreach($dst as $k=>$v)
            {
                if(is_array($v) && array_keys($v) !== range(0, count($v) - 1) && isset($src[$k]))
                {
                    // an associative array - replace!
                    $v = static::replace($src[$k], $v);
                }
                $src[$k] = $v;
            }
        }

        return $src;
    }
}
