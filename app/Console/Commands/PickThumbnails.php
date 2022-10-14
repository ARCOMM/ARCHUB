<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Missions\Mission;
use Illuminate\Support\Facades\File;

class PickThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archub:pick_thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pick thumbnails';

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
            $thumbnail = $mission->thumbnail;

            if (!$thumbnail) {
                $photos = $mission->photos();
                $photo = $photos->count() > 0 ? $photos[0] : null;

                if ($photo) {
                    if (!File::exists($photo->getPath())) {
                        $this->comment("{$mission->id} {$mission->display_name} has an invalid photo path");
                        $thumbnail = null;
                    } else {
                        $this->comment("{$mission->id} {$mission->display_name} thumbnail set to {$mission->thumbnail}");
                        $thumbnail = $photo->getUrl('thumb');
                    }

                    $mission->thumbnail = $thumbnail;
                    $mission->save();
                    continue;
                } else {
                    $this->comment("{$mission->id} {$mission->display_name} has no photos");
                }
            } else {
                $this->comment("{$mission->id} {$mission->display_name} already has a thumbnail, assumed ok");
            }
        }
    }
}
