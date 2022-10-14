<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Missions\Mission;

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
            if (!$mission->thumbnail) {
                $photos = $mission->photos();
                $thumbnail = $photos->count() > 0 ? $photos[0]->getUrl('thumb') : null;
                $mission->thumbnail = $thumbnail;
                $mission->save();
                $this->comment("{$mission->display_name} has no thumbnail, changed to {$thumbnail}");
            }
        }
    }
}
