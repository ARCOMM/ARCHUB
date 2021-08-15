<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Missions\Mission;
use Illuminate\Support\Facades\Storage;

class RenameModes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename:modes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename modes';

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
            $parts = explode('_', $mission->file_name);
            $file_mode = null;

            if ($mission->mode == "adversarial") {
                $file_mode = "TVT";
            }
            else if ($mission->mode == "arcade") {
                $file_mode = "ADE";
            }
            else if ($mission->mode == "coop") {
                $file_mode = "COOP";
            }
            else {
                throw new Exception("Invalid mode");
            }

            $new_filename = "ARC_"."{$file_mode}_".implode("_", array_slice($parts, 2));
            $mission->update([
                'file_name' => $new_filename,
            ]);

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
