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
        "PvKraj",
        "PvZalozba",
        "PvLeto",
        "jezikLong",
        "PvNaslov",
        "PvLetnik",
        "PvSt",
        "OpSistoryUrnId",
        "PvCobId",
        "oneline",
        "citatiCount",
        "citiranoCount",
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
    public static $detailsViewCitingFields = [
        "tipologyLong",
        "authorsLong",
        "OpNaslov",
        "PvKraj",
        "PvZalozba",
        "PvLeto",
    ];

    public static $tableViewFields = [
        "ID",
        "OpTipologija",
        "authorsShort",
        "OpNaslov",
        "PvLeto",
        "citatiCount",
        "citiranoCount",
    ];

    public static $tableViewFieldsPDF = [
        "ID",
        "tipologyLong",
        "authorsShort",
        "OpNaslov",
        "PvLeto",
        "citatiCount",
        "citiranoCount",
    ];

    public static $fieldsSortMap = [
        "authorsShort" => "authors.PRIIMEK.keyword",
        "authorsLong" => "authors.PRIIMEK.keyword",
        "tipologyShort" => "OpTipologija",
        "tipologyLong" => "OpTipologija",
        "jezikShort" => "OpJezik",
        "jezikLong" => "OpJezik",
        "OpNaslov" => "OpNaslov.keyword",
        "PvKraj" => "PvKraj.keyword",
    ];

    private static function getAuthorName($author) {
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
        return $priimekAndIme;
    }

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
                $priimekAndIme = self::getAuthorName($author);
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

        $zicRecord["oneline"] = self::zicDisplay_oneline($zicRecord);

        return $zicRecord;
    }

    public static function zicDisplay_oneline($zicRecord) {

        $typeV2 = Si4Util::getArg($zicRecord, "TypeV2", null);


        $firstAuthor = Si4Util::pathArg($zicRecord, "authors/0", null);
        $author = self::getAuthorName($firstAuthor);

        $naslov = Si4Util::pathArg($zicRecord, "OpNaslov", null);
        $naslovVira = Si4Util::pathArg($zicRecord, "PvNaslov", null);

        $kraj = Si4Util::pathArg($zicRecord, "PvKraj", null);
        $zalozba = Si4Util::pathArg($zicRecord, "PvZalozba", null);

        $leto = Si4Util::pathArg($zicRecord, "PvLeto", null);
        $letnik = Si4Util::pathArg($zicRecord, "PvLetnik", null);
        $st = Si4Util::pathArg($zicRecord, "PvSt", null);
        $stran = Si4Util::pathArg($zicRecord, "PvStran", null);
        $issn = Si4Util::pathArg($zicRecord, "PvISSN", null);
        $cobissId = Si4Util::pathArg($zicRecord, "PvCobId", null);
        $sistoryId = Si4Util::pathArg($zicRecord, "OpSistoryUrnId", null);

        $arr = [];

        switch ($typeV2) {
            case "serial":
                // Priimek, Ime. Naslov. Naslov vira. Leto, letn. XX., št. XX, str. XX-XX. ISSN XXXX. COBISS ID XXXXX
                if ($author) $arr[] = $author.". ";
                if ($naslov) $arr[] = $naslov.". ";
                if ($naslovVira) $arr[] = $naslovVira.". ";
                if ($leto) $arr[] = $leto;
                if ($letnik) $arr[] = ", letn. ".$letnik.".";
                if ($st) $arr[] = ", št. ".$st;
                if ($stran) $arr[] = ", str. ".$stran;
                if ($issn) $arr[] = " ISSN ".$issn.".";
                if ($cobissId) $arr[] = " COBISS ID: ".$cobissId;
                return trim(join("", $arr));
            case "mono":
                // Priimek, Ime. Naslov. Naslov vira (če je zbornik). Kraj: Založba, Leto. ISBN XXXX. COBISS ID: XXXXXX
                if ($author) $arr[] = $author.". ";
                if ($naslov) $arr[] = $naslov.". ";
                if ($naslovVira) $arr[] = $naslovVira.". ";
                if ($kraj && $zalozba) {
                    $arr[] = $kraj.": ".$zalozba;
                } else {
                    if ($kraj) $arr[] = $kraj;
                    if ($zalozba) $arr[] = $zalozba;
                }
                if ($leto) $arr[] = ", ".$leto.".";
                if ($issn) $arr[] = " ISBN ".$issn.".";
                if ($cobissId) $arr[] = " COBISS ID: ".$cobissId;
                return trim(join("", $arr));
            default:
                return "";
        }
    }

    public static function zicsDisplay($zicRecords) {
        foreach ($zicRecords as $idx => $zicRecord) {
            $zicRecords[$idx] = self::zicDisplay($zicRecord);
        }
        return $zicRecords;
    }
}

















