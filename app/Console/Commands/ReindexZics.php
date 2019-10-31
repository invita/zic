<?php

namespace App\Console\Commands;

use App\Helpers\ElasticHelpers;
use App\Models\Zic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ReindexZics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:zics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex all zic from database into elastic';

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

        if ($this->confirm('Are you sure you wish to reindex all zic citations?', true)) {

            ElasticHelpers::recreateIndex();

            $zics = Zic::all();

            $cnt = 0;
            foreach ($zics as $zic) {
                $this->info($zic["ID"]);
                Artisan::call("reindex:zic", ["zicId" => $zic["ID"]]);
                $cnt++;
            }

            $this->info("All done! Zics reindexed: {$cnt}");
        }
    }
}
