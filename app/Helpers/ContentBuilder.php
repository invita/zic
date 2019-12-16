<?php
namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * Class ContentBuilder
 *
 * @author   Matic Vrscaj
 */
class ContentBuilder
{

    /**
     * Build content html
     */
    public static function getHtmlForMenuId($menuId)
    {

        $lang = App::getLocale();
        $query = <<<HERE
            SELECT * FROM NAV_GLAVNA_TABELA_ZIC gt
                INNER JOIN NAV_STRUCTURE_ZIC s ON s.ID = gt.ID
                WHERE gt.language='{$lang}' AND s.ID = {$menuId}
                LIMIT 1
HERE;

        $items = CommonHelpers::dbToArray(DB::select($query));
        return isset($items[0]) ? $items[0]["CONTENT"] : "";
    }

    public static function getHtmlForFirstPage() {
        $lang = App::getLocale();
        $query = <<<HERE
            SELECT * FROM NAV_GLAVNA_TABELA_ZIC gt
                INNER JOIN NAV_STRUCTURE_ZIC s ON s.ID = gt.ID
                WHERE gt.language='{$lang}' AND s.FIRST_PAGE = 1
                LIMIT 1
HERE;

        $items = CommonHelpers::dbToArray(DB::select($query));
        $content = isset($items[0]) ? $items[0]["CONTENT"] : "";
        $content = self::replaceCommonPlaceholders($content);
        return $content;
    }

    public static function replaceCommonPlaceholders($content) {
        $zicCount = DB::table('ZIC_GLAVNA_TABELA_V2')->count();
        $zicLastModified = DB::table('ZIC_GLAVNA_TABELA_V2')->max('DATETIME_ADDED');
        $zicLastModifiedFormat = date("d.m.Y", strtotime($zicLastModified));

        $replaceMap = [
            "[[steviloCitatov]]" => $zicCount,
            "[[zadnjaPosodobitev]]" => $zicLastModifiedFormat,
        ];

        foreach ($replaceMap as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        return $content;
    }
}