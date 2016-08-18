<?php

class Gloebals {
    protected static $addon = 'gloebals';
    protected static $plugin = null;

    protected static $attributes = [];
    protected static $errors = [];
    protected static $messages = [];

    protected static $locales = [];

    public static function getPerm()
    {
        return static::$addon . '[' . static::$plugin . ']';
    }

    public static function getFieldName($fieldname, $isArray = false)
    {
        $fieldname = preg_replace('/[^0-9a-z\_\-]/i', '_', $fieldname);
        $fieldname = preg_replace('/_{2,}/', '_', $fieldname);
        $fieldname = trim($fieldname, '_');

        return static::$addon . (static::$plugin ? '[' . static::$plugin . ']' : '') . '[' . $fieldname . ']' . ((bool) $isArray ? '[]' : '');
    }

    public static function getFieldId($fieldname, $suffix = null)
    {
        $fieldname = preg_replace('/[^0-9a-z\_\-]/i', '_', self::getFieldName($fieldname));
        $fieldname = preg_replace('/_{2,}/', '_', $fieldname);
        $fieldname = trim($fieldname, '_');

        if($suffix !== null)
        {
            $suffix = (string) $suffix;
            $suffix = preg_replace('/[^0-9a-z\_\-]/i', '_', self::getFieldName($suffix));
            $suffix = preg_replace('/_{2,}/', '_', $suffix);
            $suffix = trim($suffix, '_');
            $fieldname.= '--' - $suffix;
        }

        return $fieldname;
    }

    protected static function getLocaleCode($lang = null)
    {
        $code = null;

        if(!empty($lang))
        {
            $lang = (string) $lang;
            if(strval((int) $lang) == $lang)
            {
                $lang = (int) $lang;
                if(rex_clang::exists($lang))
                {
                    $code = rex_clang::get($lang)->getCode();
                }
            }
            else {
                foreach(rex_clang::getAll() as $clang)
                {
                    if($clang->getCode() == $lang)
                    {
                        $code = $clang->getCode();
                    }
                }
                unset($clang);
            }
            unset($lang);
        }
        else
        {
            $code = rex::isBackend() ? substr(rex::getProperty('lang'), 0, 2) : rex_clang::get(rex_clang::getCurrentId())->getCode();
        }

        return $code;
    }

    public static function canSaveSettings()
    {
        if(rex::isBackend() && rex::getUser())
        {
            return rex::getUser()->isAdmin() || rex::getUser()->hasPerm(self::getPerm());
        }

        return false;
    }

    public static function getObject()
    {
        $addon = rex_addon::get(static::$addon);
        if(!empty(static::$plugin) && $addon->pluginExists(static::$plugin))
        {
            return $addon->getPlugin(static::$plugin);
        }

        return $addon;
    }

    public static function saveSettings()
    {
        if(self::canSaveSettings())
        {
            // user is admin - allowed to save settings...
            if(rex_post('btn_save', 'string') != '')
            {
                // btn_save is submitted, let's process the request...
                $request = rex_post(static::$addon, 'array', []);
                if(static::$plugin)
                {
                    if(isset($request[static::$plugin]))
                    {
                        $request = $request[static::$plugin];
                    }
                    else
                    {
                        $request = null;
                    }
                }

                if(!empty($request) && is_array($request))
                {
                    $save_settings = [
                    ];

                    foreach($request as $name => $settings)
                    {
                        $save_settings[$name] = $settings;
                    }
                    unset($request, $name, $settings);

                    if(!empty($save_settings))
                    {
                        if(static::getObject()->setConfig($save_settings))
                        {
                            self::addMessage(static::getObject()->i18n('settings_saved'));
                        }
                        else
                        {
                            self::addError(static::getObject()->i18n('settings_not_saved'));
                        }
                        return true;
                    }

                    unset($save_settings);
                }

                unset($request);
            }
        }
        return false;
    }

    public static function getSettings($key = null)
    {
        $config = self::getObject()->getConfig();
        if(!empty($key))
        {
            $key = (string) $key;
            return isset($config[$key]) ? $config[$key] : null;
        }

        return $config;
    }

    public static function set($key, $value)
    {
        if(!empty($key) && is_string($key))
        {
            if($value === null)
            {
                unset(self::$attributes[$key]);
            }
            else
            {
                self::$attributes[$key] = $value;
            }
        }
    }

    public static function get($key = null)
    {
        if(!empty($key))
        {
            $key = (string) $key;
            return isset(self::$attributes[$key]) ? self::$attributes[$key] : null;
        }
        return self::$attributes;
    }

    protected static function addError($msg)
    {
        self::$errors[]= $msg;
    }

    public static function hasErrors()
    {
        return !empty(self::$errors);
    }

    public static function getError($asArray = false)
    {
        return (bool) $asArray ? self::$errors : join("\n", self::$errors);
    }


    protected static function addMessage($msg)
    {
        self::$messages[]= $msg;
    }

    public static function hasMessages()
    {
        return !empty(self::$messages);
    }

    public static function getMessage($asArray = false)
    {
        return (bool) $asArray ? self::$messages : join("\n", self::$messages);
    }

    public static function i18n()
    {
        $output = '';

        $args = func_get_args();
        if(!empty($args[0]))
        {
            $string = $args[0];
            $string = preg_replace('/^' . static::$addon . '_/', '', $string);
            $string = preg_replace('/^' . static::$plugin . '_/', '', $string);

            $checkfor = [];
            if(!empty(static::$plugin))
            {
                $checkfor[] = static::$addon . '_' . static::$plugin . '_' . $string;
            }
            $checkfor[] = static::$addon . '_' . $string;
            $checkfor[] = $string;

            foreach($checkfor as $checkstr)
            {
                $checkargs = $args;
                $checkargs[0] = $checkstr;
                $translation = call_user_func_array(array(self::getObject(), 'i18n'), $checkargs);
                if(substr($translation,0,11) != '[translate:')
                {
                    return $translation;
                }
                unset($checkargs, $translation);
            }
            unset($checkfor, $checkstr, $string);

            return call_user_func_array(array(self::getObject(), 'i18n'), $args);
        }

        return '';
    }

}
