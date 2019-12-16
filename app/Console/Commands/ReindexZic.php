<?php

namespace App\Console\Commands;

use App\Helpers\ElasticHelpers;
use App\Models\Zic;
use App\Models\ZicAuthors;
use App\Models\ZicCitati;
use App\Models\ZicCitatiAuthors;
use App\Models\ZicEditors;
use Illuminate\Console\Command;

class ReindexZic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:zic {zicId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex a single zic citation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zicId = $this->argument('zicId');
        $this->info("Indexing zic {$zicId}");

        $zicDb = Zic::find($zicId);
        $zic = $zicDb ? $zicDb->toArray() : null;

        if ($zic) {

            // Load authors
            $authors = ZicAuthors::query()->where(["ZIC_ID" => $zicId])->get()->toArray();
            $zic["authors"] = $authors;

            // Load editors
            $editors = ZicEditors::query()->where(["ZIC_ID" => $zicId])->get()->toArray();
            $zic["editors"] = $editors;

            // Load citati
            $citati = ZicCitati::query()->where(["gtid" => $zicId])->get()->toArray();
            foreach ($citati as $cIdx => $citat) {
                $cId = $citat["cid"];
                // Load citati authors
                $citatiAuthors = ZicCitatiAuthors::query()->where(["GT_ID" => $zicId, "C_ID" => $cId])->get()->toArray();
                $citati[$cIdx]["citatiAuthors"] = $citatiAuthors;
            }
            $zic["citati"] = $citati;


            $dateFieldsToParse = ["ROJSTVO", "SMRT"];
            foreach ($dateFieldsToParse as $dateField) {
                $date = isset($zic[$dateField]) ? $zic[$dateField] : null;
                if ($date) {
                    try {
                        $dateYearMonth = $this->parseYearMonth($date);
                        if ($dateYearMonth["LETO"]) $zic[$dateField."_LETO"] = $dateYearMonth["LETO"];
                        if ($dateYearMonth["MESEC"]) $zic[$dateField."_MESEC"] = $dateYearMonth["MESEC"];
                    } catch (\Exception $e) {
                        $this->warn("Error trying to parseYearMonth '".$date."'");
                    }
                }
            }

            //print_r($zic);
            $indexBody = $zic;
            ElasticHelpers::indexZic($zicId, $indexBody);
        } else {
            ElasticHelpers::deleteZic($zicId);
        }
    }

    private function parseYearMonth($date) {
        $result = [
            "MESEC" => null,
            "LETO" => null
        ];

        $date = preg_replace('/\s/', "", $date);
        $dateSplit = preg_split('/[\-\.\/]/', $date);

        if (preg_match('/[\d]{1,2}.[\d]{1,2}.[\d]{4}/', $date)) {
            // DD.MM.YYYY
            $result["LETO"] = intval($dateSplit[2]);
            $result["MESEC"] = intval($dateSplit[1]);
        } else if (preg_match('/[\d]{4}.[\d]{1,2}.[\d]{1,2}/', $date)) {
            // YYYY.MM.DD
            $result["LETO"] = intval($dateSplit[0]);
            $result["MESEC"] = intval($dateSplit[1]);
        } else if (preg_match('/[\d]{1,2}.[\d]{4}/', $date)) {
            // MM.YYYY
            $result["LETO"] = intval($dateSplit[1]);
            $result["MESEC"] = intval($dateSplit[0]);
        } else if (preg_match('/[\d]{4}.[\d]{1,2}/', $date)) {
            // YYYY.MM
            $result["LETO"] = intval($dateSplit[0]);
            $result["MESEC"] = intval($dateSplit[1]);
        } else if (preg_match('/[\d]{4}/', $date)) {
            // YYYY only
            $result["LETO"] = intval($date);
        }

        return $result;
    }
}
