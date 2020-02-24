<?php

namespace App\Console\Commands;

use App\Helpers\ElasticHelpers;
use App\Models\Zic;
use App\Models\ZicAuthors;
use App\Models\ZicCitati;
use App\Models\ZicCitatiAuthors;
use App\Models\ZicEditors;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

            $zicMin = array_merge([], $zic);


            $zic["citati"] = $citati;


            // Citati count
            $zic["citatiCount"] = count($citati);


            // Citirano count
            $OpCobId = isset($zic["OpCobId"]) ? $zic["OpCobId"] : null;
            $naslov = isset($zic["OpNaslov"]) ? $zic["OpNaslov"] : null;
            $leto = isset($zic["PvLeto"]) ? $zic["PvLeto"] : null;
            if ($OpCobId || $naslov && $leto) {
                $query = DB::raw("SELECT COUNT(DISTINCT gtid, cid) AS CNT FROM ZIC_CITATI_V2 ".
                                 "WHERE COBISSid = :cobId OR naslov0 = :naslov");
                $citiranoCountDB = DB::selectOne($query, [
                    "cobId" => $OpCobId,
                    "naslov" => $naslov,

                    // AND leto = :leto
                    //"leto" => $leto
                ]);

                $zic["citiranoCount"] = $citiranoCountDB->CNT;
            } else {
                $zic["citiranoCount"] = 0;
            }
            /*
            $OpNaslov = isset($zic["OpNaslov"]) ? $zic["OpNaslov"] : "";
            $query = DB::raw("SELECT COUNT(DISTINCT GT_ID, C_ID) AS CNT FROM ZIC_CITATI_TITLES_V2 WHERE TRIM(NAZIV) = :val");
            $citiranoCountDB = DB::selectOne($query, [ "val" => $OpNaslov ]);
            $zic["citiranoCount"] = $citiranoCountDB->CNT;
            */


            //print_r($zic);
            ElasticHelpers::indexZic($zicId, $zic);

            foreach ($citati as $cIdx => $citat) {
                $cId = $citat["cid"];
                $citat["zic"] = $zicMin;
                ElasticHelpers::indexCitat($zicId, $cId, $citat);
            }


        } else {
            ElasticHelpers::deleteZic($zicId);
        }
    }

}
