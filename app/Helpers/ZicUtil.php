<?php
namespace App\Helpers;

/**
 * Class ZicHelpers
 * @author Matic Vrscaj
 */
class ZicUtil
{

    public static $detailsViewFields = [
        "ID",
        "authorsLong",
        "OpNaslov",
        "tipologyLong",
        "PvLeto",
        "PvKraj",
        "PvZalozba",
        "jezikLong",
        "PvNaslov",
        "PvLetnik",
        "PvSt",
        "OpSistoryUrnId",
        "PvCobId",
        "citiranoCount",
        "citatiCount"
    ];
    public static $detailsViewCitatiFields = [
        "str",
        "avtor0",
        "naslov0",
        "vir",
        "kraj",
        "zalozba",
        "leto",
    ];

    public static $tableViewFields = [
        "ID",
        "OpTipologija",
        "authorsShort",
        "OpNaslov",
        "PvLeto",
        "citatiCount",
        "citiranoCount"
    ];


    public static function zicDisplay($zicRecord) {

        if (!$zicRecord) return null;

        $zicRecord["authorsShort"] = "";
        $zicRecord["authorsLong"] = "";
        $zicRecord["tipologyShort"] = "";
        $zicRecord["tipologyLong"] = "";
        $zicRecord["jezikShort"] = "";
        $zicRecord["jezikLong"] = "";


        // Prepare authors
        if (isset($zicRecord["authors"]) && $zicRecord["authors"]) {

            foreach ($zicRecord["authors"] as $author) {
                $ime = Si4Util::getArg($author, "IME", "");
                $priimek = Si4Util::getArg($author, "PRIIMEK", "");

                $priimekAndIme = "";
                if ($ime && $priimek) {
                    $priimekAndIme = $priimek.", ".$ime;
                } else if ($ime) {
                    $priimekAndIme = $ime;
                } else if ($priimek) {
                    $priimekAndIme = $priimek;
                }

                if (!$zicRecord["authorsShort"]) $zicRecord["authorsShort"] = $priimekAndIme;

                if ($zicRecord["authorsLong"]) $zicRecord["authorsLong"] .= " ;\n";
                $zicRecord["authorsLong"] .= $priimekAndIme;
            }
        }

        // Tipologija
        if (isset($zicRecord["OpTipologija"])) {
            $tipologies = SifHelpers::getTipologies();
            if (isset($tipologies[$zicRecord["OpTipologija"]])) {
                $sifStr = strval($zicRecord["OpTipologija"]); // "101"
                $sifDot = $sifStr[0].".".substr($sifStr, 1); // "1.01"
                $tipoDescription = $tipologies[$zicRecord["OpTipologija"]];
                $zicRecord["tipologyShort"] = $sifDot;
                $zicRecord["tipologyLong"] = $tipoDescription;
            }
        }

        // Jezik
        if (isset($zicRecord["OpJezik"])) {

            $langSif = SifHelpers::getLanguages();

            if (isset($langSif[$zicRecord["OpJezik"]])) {
                $langFromSif = isset($langSif[$zicRecord["OpJezik"]]) ? $langSif[$zicRecord["OpJezik"]] : "";
                $langExp = explode(" - ", $langFromSif);

                if (count($langExp) >= 2) {
                    $zicRecord["jezikShort"] = trim($langExp[0]);
                    $zicRecord["jezikLong"] = ucfirst(trim($langExp[1])) ." (".$zicRecord["jezikShort"].")";
                }

            }

        }

        // Sistory Link
        if (isset($zicRecord["OpSistoryUrnId"])) {
            $zicRecord["OpSistoryUrnId_link"] = "https://www.sistory.si/11686/".$zicRecord["OpSistoryUrnId"];
        }

        // Cobiss Link
        if (isset($zicRecord["PvCobId"])) {
            $zicRecord["PvCobId_link"] = "http://www.cobiss.si/scripts/cobiss?command=DISPLAY&base=cobib&rid=".$zicRecord["PvCobId"];
        }

        return $zicRecord;
    }

    public static function zicsDisplay($zicRecords) {
        foreach ($zicRecords as $idx => $zicRecord) {
            $zicRecords[$idx] = self::zicDisplay($zicRecord);
        }
        return $zicRecords;
    }
}

















