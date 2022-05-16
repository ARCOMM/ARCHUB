<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\JoinRequests\JoinStatus;
use App\Models\JoinRequests\JoinRequest;

class MigrateOldApps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apps:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates apps from the old database.';

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
        $apps = DB::connection('mysql_old')->table('apps')->get();

        foreach ($apps as $item) {
            if ($item->appStatus == 'Blacklisted') {
                continue;
            }

            $jr = new JoinRequest();
            $jr->created_at = $item->appTimestamp;
            $jr->updated_at = $item->appTimestamp;
            $jr->name = $item->appName;
            $jr->age = (int)$item->appAge;
            $jr->location = $item->appCountry;
            $jr->email = $item->appEmail;
            $jr->steam = $item->appSteam;
            $jr->available = (Str::contains(strtolower($item->appAvailable), 'yes')) ? true : false;
            $jr->apex = (Str::contains(strtolower($item->appApex), 'yes')) ? true : false;
            $jr->groups = (Str::contains(strtolower($item->appGroups), 'yes')) ? true : false;
            $jr->experience = $item->appExperience;
            $jr->bio = $item->appBio;
            $jr->source_id = $item->appSource;
            $jr->source_text = $item->appSourceData;
            $jr->status_id = JoinStatus::where('text', '=', $item->appStatus)->first()->id;
            $jr->save();
        }
    }
}
