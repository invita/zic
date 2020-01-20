<?php
namespace App\Helpers;
use Illuminate\Support\Facades\DB;

/**
 * Class CommonHelpers
 * @author Matic Vrscaj
 */
class SifHelpers
{
    private static $sifTipologies = null;
    public static function getTipologies() {
        if (!self::$sifTipologies) {
            $query = "SELECT * FROM ZIC_SIF_TPL_V2";
            $items = DB::select($query);
            $result = [];
            foreach ($items as $i => $item) {
                $itemArr = (array)$item;
                $ID = $itemArr["ID"];
                $result[$ID] = $itemArr["NAZIV"];
            }
            self::$sifTipologies = $result;
        }
        return self::$sifTipologies;
    }

    private static $sifLanguages = null;
    public static function getLanguages() {
        if (!self::$sifLanguages) {
            $query = "SELECT * FROM ZIC_SIF_LNG_V2";
            $items = DB::select($query);
            $result = [];
            foreach ($items as $i => $item) {
                $itemArr = (array)$item;
                $ID = $itemArr["ID"];
                $result[$ID] = $itemArr["NAZIV"];
            }
            self::$sifLanguages = $result;
        }
        return self::$sifLanguages;
    }
}