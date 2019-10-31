<?php

namespace App\Console\Commands;

use App\Helpers\ElasticHelpers;
use Illuminate\Console\Command;

class RecreateIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reindex:recreate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreate empty index only';

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
        if ($this->confirm('Are you sure you wish to recreate zic index?', true)) {
            ElasticHelpers::recreateIndex();
            $this->info("Index '".env("SI4_ELASTIC_ZIC_INDEX", "zic")."' recreated");
        }
    }
}
