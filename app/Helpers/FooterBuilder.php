<?php
namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * Class FooterBuilder
 *
 * @author   Matic Vrscaj
 */
class FooterBuilder
{

    /**
     * Build footer html
     */
    public static function getHtml()
    {
        $rootId = 0;
        //$baseUrl = "http://www.sistory.si";
        $baseUrl = "";
        $lang = App::getLocale();

        $query = <<<HERE
            SELECT NAV_GLAVNA_TABELA_ZIC.id, parent_id, title, link
            FROM NAV_GLAVNA_TABELA_ZIC
            INNER JOIN NAV_STRUCTURE_ZIC ON NAV_STRUCTURE_ZIC.ID = NAV_GLAVNA_TABELA_ZIC.ID
            WHERE language='{$lang}' AND STATUS=1
            ORDER BY NAV_GLAVNA_TABELA_ZIC.order
HERE;

        $items = DB::select($query);

        // Convert stdObjects to arrays
        foreach ($items as $i => $item) {
            $items[$i] = (array)$item;
            if ($items[$i]["link"])
                $items[$i]["link"] = $baseUrl.$items[$i]["link"];
            else
                $items[$i]["link"] = $baseUrl."/?menuId=".$items[$i]["id"];
        }
        //return print_r($items, true);

        $nav = [];

        // Get root nodes
        foreach ($items as $i => $item) {
            if ($item["parent_id"] == $rootId)
                $nav[$item["id"]] = $item;
        }

        // Get level1 nodes
        foreach ($nav as $nId => $navItem) {
            foreach ($items as $i => $item) {
                if ($item["parent_id"] == $nav[$nId]["id"]) {
                    if (!isset($nav[$nId]["children"])) $nav[$nId]["children"] = [];
                    $nav[$nId]["children"][$item["id"]] = $item;
                }
            }
        }

        //return print_r($nav, true);

        $html  = array();
        foreach ($nav as $nId => $navItem) {
            $html[] = '<div class="large-6 medium-6 small-6 columns"><ul class="firstUL"><li>';
            $html[] = '<h4 class="title">'.$navItem['title'].'</h4>';

            $html[] = '<ul class="no-bullet">';
            if (isset($navItem["children"]) && $navItem["children"]) {
                foreach ($navItem["children"] as $cId => $childItem) {
                    $html[] = '<li><a href="'.$childItem["link"].'">'.$childItem["title"].'</a></li>';
                }
            }
            $html[] = '</ul>';

            $html[] = '</li></ul></div>';
        }

        return implode("\r\n", $html);

    }
}