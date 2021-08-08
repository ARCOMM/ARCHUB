<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Missions\Mission;
use Illuminate\Support\Facades\Storage;

class ConvertIdToRevisions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts the IDs used in paths to revisions.';

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
            $newCloudPbo = "missions/{$mission->user_id}/{$mission->id}/{$mission->exportedName('pbo')}";
            $newCloudZip = "missions/{$mission->user_id}/{$mission->id}/{$mission->exportedName('zip')}";

            if (Storage::cloud()->exists($mission->cloud_pbo) && !Storage::cloud()->exists($newCloudPbo)) {
                Storage::cloud()->move($mission->cloud_pbo, $newCloudPbo);
            }

            $mission->update([
                'pbo_path' => $newCloudPbo,
                'cloud_pbo' => $newCloudPbo,
                'cloud_zip' => $newCloudZip,
            ]);
        }
    }
}
