<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Missions\Mission;
use Illuminate\Support\Facades\Storage;

class StoreBriefings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archub:store_briefings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store briefings';

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
        foreach (Mission::all() as $mission) {
            $briefingsArray = json_decode($mission->briefings);
            $mission->storeBriefings($briefingsArray);
        }
    }
}
