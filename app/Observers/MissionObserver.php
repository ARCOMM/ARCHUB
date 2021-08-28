<?php

namespace App\Observers;

use App\Models\Missions\Mission;
use Storage;

class MissionObserver
{
    /**
     * Listen to the mission deleting event.
     *
     * @param  App\Models\Missions\Mission  $mission
     * @return void
     */
    public function deleting(Mission $mission)
    {
        $path = "missions/{$mission->user_id}/{$mission->id}";

        Storage::cloud()->delete("{$path}/{$mission->exportedName()}");

        foreach (['pbos' => 'pbo', 'zips' => 'zip'] as $dir => $name) {
            if (file_exists(public_path("downloads/{$dir}"))) {
                Storage::disk('downloads')->delete("{$dir}/{$mission->exportedName()}");
            }
        }

        Storage::deleteDirectory($path);
    }
}
