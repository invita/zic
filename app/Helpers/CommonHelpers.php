<?php
namespace App\Helpers;

/**
 * Class CommonHelpers
 *
 * @author   Matic Vrscaj
 */
class CommonHelpers
{

    /**
     * Get session language
     */
    /*
    public static function getLanguage()
    {
        $lang = request()->input('lang');
        if ($lang) {
            request()->session()->put("lang", $lang);
        } else {
            $lang = request()->session()->get("lang");
            if (!$lang) $lang = "svn";
        }
        return $lang;
    }
    public static function getLanguage2Char()
    {
        return self::getLanguage() == "eng" ? "en" : "sl";
    }

    public static function getLocale() {

    }

    public static function setLocale($locale) {

    }
    */
    public static function dbToArray($arrayOfObjs) {
        foreach ($arrayOfObjs as $i => $item)
            $arrayOfObjs[$i] = (array)$item;
        return $arrayOfObjs;
    }

}