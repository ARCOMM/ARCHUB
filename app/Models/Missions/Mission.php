<?php

namespace App\Models\Missions;

use File;
use Storage;
use \stdClass;
use Carbon\Carbon;
use App\Helpers\ArmaConfig;
use App\Helpers\ArmaScript;
use App\Models\Portal\User;
use Spatie\MediaLibrary\Media;
use App\Helpers\ArmaConfigError;
use App\Notifications\MissionUpdated;
use App\Notifications\MissionVerified;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\MissionPublished;
use App\Notifications\MissionNoteAdded;
use Illuminate\Notifications\Notifiable;
use App\Notifications\MentionedInComment;
use Kingsley\Mentions\Traits\HasMentions;
use Kingsley\References\Models\Reference;
use App\Notifications\MissionCommentAdded;
use App\Models\Operations\OperationMission;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use App\Helpers\PBOMission\PBOMission;
use App\Helpers\PBOMission\PBOFile\PBOFile;

class Mission extends Model implements HasMediaConversions
{
    use Notifiable,
        HasMediaTrait,
        HasMentions;

    public $factions = [
        0 => "Opfor",
        1 => "Blufor",
        2 => "Indfor",
        3 => "Civilian"
    ];

    /**
     * Guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'last_played'
    ];

    /**
     * Attribute casts.
     *
     * @var array
     */
    protected $casts = [
        'loadout_addons' => 'array'
    ];

    /**
     * The addons that should be warned from using.
     *
     * @var array
     */
    public $addonWarnings = [
        'rhs'
    ];

    /**
     * The loadout role map.
     *
     * @var array
     */
    protected $roles = [
        'all' => 'Everyone',
        'co' => 'Commander',
        'dc' => 'Squad Leader',
        'ftl' => 'Fireteam Leader',
        'm' => 'Medic',
        'fac' => 'Forward Air Controller',
        'r' => 'Rifleman',
        'ar' => 'Automatic Rifleman',
        'aar' => 'Assistant Automatic Rifleman',
        'rat' => 'Rifleman (AT)',
        'mmgtl' => 'Medium MG Team Leader',
        'mmgg' => 'Medium MG Gunner',
        'mmgab' => 'Medium MG Ammo Bearer',
        'mattl' => 'Medium AT Team Leader',
        'matg' => 'Medium AT Missile Specialist',
        'matab' => 'Medium AT Assistant Missile Specialist',
        'mtrl' => 'Mortar Team Leader',
        'mtrg' => 'Mortar Gunner',
        'mtra' => 'Mortar Assistant',
        'p' => 'Pilot',
        'cp' => 'Co-Pilot',
        'vc' => 'Vehicle Commander',
        'vd' => 'Vehicle Driver',
        'vg' => 'Vehicle Gunner'
    ];

    /**
     * Game mode full texts.
     *
     * @var array
     */
    protected $gamemodes = [
        'coop' => 'Cooperative',
        'adversarial' => 'Adversarial',
        'preop' => 'Pre-Operation'
    ];

    /**
     * Appended attributes.
     *
     * @var array
     */
    protected $appends = [
        'ref'
    ];

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::created(function (Model $model) {
            $model->reference()->save(
                new Reference([
                    'hash' => $model->makeReferenceHash()
                ])
            );
        });
    }

    /**
     * Gets the reference for the model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function reference()
    {
        return $this->morphOne(Reference::class, 'model');
    }

    /**
     * Makes a new reference hash.
     *
     * @return string
     */
    public function makeReferenceHash()
    {
        if (property_exists($this, 'referencePrefix')) {
            if (is_null($this->referencePrefix)) {
                return str_random(12);
            } else {
                return $this->referencePrefix . '_' . str_random(12);
            }
        }

        if (config('references.prefix')) {
            $prefix = substr(strtolower(class_basename(get_class($this))), 0, 3);

            return $prefix . '_' . str_random(12);
        }

        return str_random(12);
    }

    /**
     * Gets the ref attribute.
     *
     * @return string
     */
    public function getRefAttribute()
    {
        return optional($this->reference)->hash;
    }

    /**
     * Discord notification channel.
     *
     * @return any
     */
    public function routeNotificationForDiscord()
    {
        return config('services.discord.channel_id');
    }

    /**
     * Media library image conversions.
     *
     * @return void
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->width(384)
            ->height(384)
            ->quality(100)
            ->nonQueued()
            ->performOnCollections('images');
    }

    /**
     * Gets the loadout addons attribute.
     *
     * @return array
     */
    public function getLoadoutAddonsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        return json_decode(json_decode($value));
    }

    /**
     * Gets the unread comments on the mission.
     *
     * @return Collection App\Models\Missions\MissionComment
     */
    public function unreadComments()
    {
        $mission = $this;

        $filtered = auth()->user()->unreadNotifications->filter(function ($item) use ($mission) {
            return
                $item->type == MissionCommentAdded::class &&
                $item->data['comment']['mission_id'] == $mission->id;
        });

        return $filtered;
    }

    /**
     * Gets all past missions (last played is in past and not null).
     *
     * @return Collection App\Models\Missions\Mission
     */
    public static function allPast()
    {
        return static::where('last_played', '!=', null)
            ->where('last_played', '<', Carbon::now()->toDateTimeString())
            // ->where('verified', true)
            ->orderBy('last_played', 'desc')
            ->get();
    }

    /**
     * Gets all new missions (last played is null).
     *
     * @return Collection App\Models\Missions\Mission
     */
    public static function allNew()
    {
        return static::where('last_played', null)
            // ->where('verified', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Checks whether the mission is new.
     *
     * @return boolean
     */
    public function isNew()
    {
        return is_null($this->last_played);
    }

    /**
     * Gets the missions map.
     *
     * @return App\Models\Missions\Map
     */
    public function map()
    {
        return $this->belongsTo(Map::class);
    }

    /**
     * Gets the verified by user model.
     *
     * @return App\Models\Portal\User
     */
    public function verifiedByUser()
    {
        return User::where('id', $this->verified_by)->first();
    }

    /**
     * Gets the mission's user (author).
     *
     * @return App\Models\Portal\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the full mission URL.
     *
     * @return string
     */
    public function url($uri = '')
    {
        return url("/hub/missions/{$this->id}/{$uri}");
    }

    /**
     * Checks whether the mission belongs to the authenticated user.
     *
     * @return boolean
     */
    public function isMine()
    {
        return $this->user_id == auth()->user()->id;
    }

    /**
     * Gets all mission comments.
     *
     * @return Collection App\Models\Missions\MissionComment
     */
    public function comments()
    {
        return $this->hasMany(MissionComment::class)
            ->orderBy('updated_at');
    }

    /**
     * Gets all notes for the mission.
     *
     * @return Collection App\Models\Missions\MissionNote
     */
    public function notes()
    {
        return $this->hasMany(MissionNote::class);
    }

    /**
     * Gets all revisions for the mission.
     *
     * @return Collection App\Models\Missions\MissionRevision
     */
    public function revisions()
    {
        return $this->hasMany('App\Models\Missions\MissionRevision');
    }

    /**
     * Gets the mission banner URL.
     *
     * @return string
     */
    public function banner()
    {
        if (config('app.env') === 'local') {
            return '';
        }

        $media = $this->photos();

        if (count($media) > 0) {
            return $media[0]->getUrl();
        } else {
            return '';
        }
    }

    /**
     * Returns true if this is an arcore mission
     * 
     * @return bool
     */
    public function isLegacy()
    {
        return !is_null($this->sqm_json);
    }

    /**
     * Returns page url or nothing if it is a legacy mission
     * 
     * @return string
     */
    public function getPageUrl() 
    {
        if($this->isLegacy()) {
            return '#';
        }

        return url('/hub/missions/' . $this->id);
    }

    /**
     * Gets the mission thumbnail URL.
     *
     * @return string
     */
    public function thumbnail()
    {
        if (config('app.env') === 'local') {
            return '';
        }

        $media = $this->photos();

        if (count($media) > 0) {
            return $media[0]->getUrl('thumb');
        }

        if (!is_null($this->map->image_2d)) {
            return url($this->map->image_2d);
        }

        // return url('/images/arcomm-placeholder.jpg');
        return '';
    }

    /**
     * Gets a full text string of the game mode.
     *
     * @return string
     */
    public function fulltextGamemode()
    {
        return $this->gamemodes[strtolower($this->mode)];
    }

    /**
     * Gets the exported name of the file following the mission name format.
     *
     * @return string
     */
    public function exportedName($format = 'pbo')
    {
        $revisions = $this->revisions()->count();

        $download = 'ARC_' .
            strtoupper($this->mode == 'adversarial' ? 'tvt' : $this->mode) . '_' .
            studly_case($this->display_name) . '_' .
            trim(substr($this->user->username, 0, 4)) . '_' .
            $revisions . '.' .
            $this->map->class_name . '.' . $format;

        return $download;
    }

    /**
     * Gets all videos for the mission.
     * Sorted latest first.
     *
     * @return Collection App\Models\Portal\Video
     */
    public function videos()
    {
        return $this->hasMany('App\Models\Portal\Video')->orderBy('created_at', 'desc');
    }

    /**
     * Gets all photos for the mission.
     *
     * @return Collection
     */
    public function photos()
    {
        return $this->getMedia('images');
    }

    /**
     * Sets the given briefing faction to locked or unlocked.
     *
     * @return void
     */
    public function lockBriefing($factionId, $state)
    {
        $this->{'locked_' . strtolower($this->factions[$factionId]) . '_briefing'} = $state;
        $this->save();
    }

    /**
     * Gets the user's draft for the mission.
     *
     * @return App\Models\Missions\MissionComment
     */
    public function draft()
    {
        $comment = MissionComment::
            where('mission_id', $this->id)
            ->where('user_id', auth()->user()->id)
            ->where('published', false)
            ->first();

        return $comment;
    }

    /**
     * Checks whether the given mission exists in an operation.
     *
     * @return boolean
     */
    public function existsInOperation()
    {
        $item = OperationMission::where('mission_id', $this->id)->first();

        if ($item) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the full path of the armake exe.
     *
     * @return string
     */
    public static function armake()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return resource_path('utils/armake.exe');
        } else {
            return 'armake';
        }
    }

    /**
     * Creates the downloadable file and returns its full URL.
     *
     * @return string
     */
    public function download($format = 'pbo')
    {
        $path_to_file = ($format == 'pbo') ? $this->cloud_pbo : $this->cloud_zip;

        if (strlen($path_to_file) == 0) {
            $path_to_file = "missions/{$this->user_id}/{$this->id}/{$this->exportedName($format)}";
        }

        $command = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ?
            'gsutil ' :
            '/usr/bin/python /usr/lib/google-cloud-sdk/bin/bootstrapping/gsutil.py ';

        $signed_url = shell_exec(
            $command .
            'signurl -d 10m ' .
            base_path('gcs.json') .
            ' gs://archub/' . $path_to_file .
            ' 2>&1' // Needed to get output
        );

        return trim(preg_replace('/([\s\S]+)https:\/\/storage/', 'https://storage', $signed_url));
    }

    /**
     * Unpacks the mission PBO and returns the absolute path of the folder.
     * 
     * @return string
     */
    public function unpack($dirname = '', $pbo_path = '')
    {
        $pbo_path = ($pbo_path == '') ? storage_path("app/{$this->pbo_path}") : $pbo_path;
        $unpacked = ($dirname == '') ?
            storage_path("app/missions/{$this->user_id}/{$this->id}/unpacked") :
            storage_path("app/missions/{$this->user_id}/{$dirname}");

        // It should always be the most up-to-date
        // as we delete unpacked after using them
        if (file_exists($unpacked)) {
            return $unpacked;
        }

        // Unpack the PBO
        shell_exec(static::armake() . ' unpack -f ' . $pbo_path . ' ' . $unpacked);

        $workingDir = getcwd();
        chdir($unpacked);

        // Debinarize mission.sqm
        // If it's not binned, armake exits gracefully
        shell_exec(static::armake() . ' derapify -f mission.sqm mission.sqm');

        chdir($workingDir);

        return $unpacked;
    }

    /**
     * Deletes the unpacked mission directory.
     *
     * @return void
     */
    public function deleteUnpacked($dirname = '')
    {
        $unpacked = ($dirname == '') ?
            storage_path("missions/{$this->user_id}/{$this->id}/unpacked") :
            storage_path("missions/{$this->user_id}/{$dirname}");

        Storage::deleteDirectory($unpacked);
    }

    /**
     * Gets the missions weather
     *
     * @return object
     */
    public function missionWeather() 
    {
        return $this->weather;
    }

    /**
     * Gets the mission's addon dependencies.
     *
     * @return \Illuminate\Support\Collection
     */
    public function addons()
    {
        return json_decode($this->dependencies);
    }

    public function GetBriefings() 
    {
        return json_decode($this->briefings);
    }

    /**
     * Checks if the mission contains the given addon (loose)
     *
     * @return boolean
     */
    public function hasAddon($key)
    {
        foreach ($this->addons() as $addonName) {
            if (starts_with(strtolower($addonName), strtolower($key))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Stores the decoded mission config objects in the session.
     * Used for optimisation.
     *
     * @return void
     */
    public function storeConfigs($pbo_path)
    {
        //parse mission.sqm
        $mission = new PBOMission($pbo_path);
        $contents = $mission->export();

        if ($mission->error) {
            return new ArmaConfigError($mission->error);
        }

        $validationError = $this->ValidateMissionContents($contents);
        if (!is_null($validationError)) {
            return new ArmaConfigError($validationError);
        }

        $this->display_name = $contents['mission']['name'];
        if(array_key_exists('description', $contents['mission'])) {
            $this->summary = $contents['mission']['description'];
        }

        $briefingsArray = $this->parseBriefings($contents['mission']['briefings']);
        $this->briefings = json_encode($briefingsArray);
        $this->dependencies = json_encode($contents['mission']['dependencies']);
        if(array_key_exists('date', $contents['mission'])) {
            $this->date = $contents['mission']['date'];
        }
        if(array_key_exists('time', $contents['mission'])) {
            $this->time = $contents['mission']['time'];
        }
        $this->weather = json_encode($contents['mission']['weather']);

        $this->save();

        // Move to cloud storage
        $this->deployCloudFiles();

        return $this;
    }

    private function ValidateMissionContents($contents) 
    {
        $files = $contents['pbo']['files'];
        $expectedFiles = ["mission.sqm", "description.ext"];
        $expectedFolderCount = ["briefing\\" => ["count" => 0, "ignore" => ["briefing\\briefing_example.sqf"]]];
        
        switch ($this->mode) {
            case "preop":
                $expectedFolderCount["briefing\\"]["count"] = 0;
                break;
            case "coop":
                $expectedFolderCount["briefing\\"]["count"] = 2;
                break;
            case "adversarial":
                $expectedFolderCount["briefing\\"]["count"] = 3;
                break;
            default:
                return "Unknown mission mode when validating mission contents";
        }

        $errorText = "";

        foreach ($expectedFiles as $file) {
            if (!array_key_exists($file, $files)) {
                $errorText .= sprintf("%s is missing\n", $file);
            }
        }

        foreach ($expectedFolderCount as $folder => $expectations) {
            $fileCount = $this->countFilesInFolder($files, $folder, $expectations["ignore"]);

            if ($fileCount < $expectations["count"]) {
                $errorText .= sprintf("%s has %d files, should have %d\n", $folder, $fileCount, $expectations["count"]);
            }
        }

        if (strlen($errorText) > 0) {
            return $errorText;
        }
        return null;
    }

    private function countFilesInFolder($files, $folder, $ignore) {
        $count = 0;
        foreach ($files as $file) {
            if ((!in_array($file["path"], $ignore)) && (substr($file["path"], 0, strlen($folder)) === $folder)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Deploys the mission files to cloud storage.
     * - PBO Download
     * - ZIP Download
     *
     * @return any
     */
    public function deployCloudFiles()
    {
        $qualified_pbo = "missions/{$this->user_id}/{$this->id}/{$this->exportedName()}";

        // Mission PBO
        Storage::cloud()->put(
            $qualified_pbo,
            file_get_contents(storage_path("app/{$this->pbo_path}"))
        );

        $this->cloud_pbo = $qualified_pbo;
        $this->pbo_path = $qualified_pbo;
        $this->save();
    }

    /**
     * Computes an array of options and returns the lowest true result.
     *
     * @return string
     */
    protected static function computeLessThan($value, $options)
    {
        foreach ($options as $text => $level) {
            if ($value <= $level) {
                return $text;
            }
        }
    }

    /**
     * Gets the fog text.
     *
     * @return string
     */
    public function fog()
    {
        return static::computeLessThan(
            $this->missionWeather()['start']['fogDecay'] ?? 0,
            [
                '' => 0.0,
                'Light Fog' => 0.1,
                'Medium Fog' => 0.3,
                'Heavy Fog' => 0.5,
                'Extreme Fog' => 1.0
            ]
        );
    }

    /**
     * Gets the overcast text.
     *
     * @return string
     */
    public function overcast()
    {
        return static::computeLessThan(
            $this->missionWeather()['start']['weather'] ?? 0,
            [
                'Clear Skies' => 0.1,
                'Partly Cloudy' => 0.3,
                'Heavy Clouds' => 0.6,
                'Stormy' => 1.0
            ]
        );
    }

    /**
     * Gets the rain text.
     *
     * @return string
     */
    public function rain()
    {
        $startRain = $this->missionWeather()['start']['rain'] ?? 0;
        $forecastRain = $this->missionWeather()['forecast']['rain'] ?? 0;
        $diff = $forecastRain - $startRain;

        return static::computeLessThan(
            $diff,
            [
                '' => 0,
                'Slight Drizzle' => 0.2,
                'Drizzle' => 0.4,
                'Rain' => 0.6,
                'Showers' => 1
            ]
        );
    }

    /**
     * Gets the weather text.
     *
     * @return string
     */
    public function weather()
    {
        return $this->overcast() . (($this->fog() == '') ? '' : ', ' . $this->fog()) . (($this->rain() == '') ? '' : ', ' . $this->rain());
    }

    /**
     * Gets the weather image name.
     *
     * @return string
     */
    public function weatherImage()
    {
        return url('/images/weather/' . ([
            'Clear Skies' => 'clear',
            'Partly Cloudy' => 'partly sunny',
            'Heavy Clouds' => 'partly cloudy',
            'Stormy' => 'cloudy',
            'Clear Skies, Slight Drizzle' => 'slight drizzle',
            'Clear Skies, Drizzle' => 'light rain',
            'Clear Skies, Rain' => 'rain',
            'Clear Skies, Showers' => 'showers',
            'Partly Cloudy, Slight Drizzle' => 'slight drizzle',
            'Partly Cloudy, Drizzle' => 'light rain',
            'Partly Cloudy, Rain' => 'rain',
            'Partly Cloudy, Showers' => 'showers',
            'Heavy Clouds, Slight Drizzle' => 'slight drizzle',
            'Heavy Clouds, Drizzle' => 'light rain',
            'Heavy Clouds, Rain' => 'rain',
            'Heavy Clouds, Showers' => 'showers',
            'Stormy, Slight Drizzle' => 'slight drizzle',
            'Stormy, Drizzle' => 'light rain',
            'Stormy, Rain' => 'rain',
            'Stormy, Showers' => 'showers'
        ])[$this->overcast() . (($this->rain() == '') ? '' : ', ' . $this->rain())] . '.png');
    }

    /**
     * Gets the mission SQM date.
     *
     * @return string
     */
    public function date()
    {
        return $this->date;
    }

    /**
     * Gets the mission SQM time.
     *
     * @return string
     */
    public function time()
    {
        return $this->time;
    }

    /**
     * Gets all briefing factions that aren't locked (unless admin).
     *
     * @return array
     */
    public function briefingFactions()
    {
        $filledFactions = [];
        $factionLocks = [
            0 => $this->locked_0_briefing,
            1 => $this->locked_1_briefing,
            2 => $this->locked_2_briefing,
            3 => $this->locked_3_briefing
        ];

        $briefings = $this->GetBriefings();

        if($briefings != null) {
            foreach($briefings as $briefing) {
                $factionId = $this->parseFactionId($briefing[1][0]);
                if(!$factionLocks[$factionId] || auth()->user()->hasPermission('mission:view_locked_briefings') || $this->isMine()) {
                    $nav = new stdClass();
                    $nav->name = $briefing[0];
                    $nav->faction = $factionId;
                    $nav->locked = $factionLocks[$factionId];

                    array_push($filledFactions, $nav);
                }
            }
        }

        return $filledFactions;
    }

    private function parseFactionId($rawFaction) 
    {
        //Briefings are per faction numbers
        if(is_int($rawFaction)) return $rawFaction;

        return 3;
    }

    /**
     * Gets the given faction's briefing subjects and content.
     *
     * @return array
     */
    public function briefing($factionId)
    {
        $filledSubjects = [];
        $briefing = $this->GetBriefing($factionId);
        $factionName = $briefing[0];
        $contents = $briefing[3];

        foreach($contents as $name => $section) {
            $formattedSection = new stdClass();
            $formattedSection->title = $name;
            $formattedSection->paragraphs = $this->FormatParagraphs($section);
            $formattedSection->locked = $this->{'locked_' . $factionId . '_briefing'};
            array_push($filledSubjects, $formattedSection);
        }

        return $filledSubjects;
    }

    private function GetBriefing($factionId) {
        $availableBriefings = $this->GetBriefings();
        foreach($availableBriefings as $briefing) {
            $id = $this->parseFactionId($briefing[1][0]);
            if($id == $factionId) {
                return $briefing;
            }
        }
        
        throw new Exception("Faction does not have a briefing file specified");
    }

    private function FormatParagraphs($paragraph) {
        $paragraph = str_replace("<font size='18'>", "<b>", $paragraph);

        return str_replace("</font>", "</b>", $paragraph);
    }

    private function parseBriefings($briefings) {   
        for($i = 0; $i < count($briefings); $i++) {
            $briefings[$i][3] = $this->parseBriefing($briefings[$i][3]);
        }

        return $briefings;
    }

    private function parseBriefing($briefing) {
        preg_match_all("~\"diary\", [\S\s]+?(?=\]\s*\]\s*;)~", $briefing, $diaryMatches);

        $dict = array();
        $diaries = $diaryMatches[0];

        foreach ($diaries as $diary) {
            preg_match_all("~\"([^\"]+)\"~", $diary, $quotes);
            $dict[$quotes[1][1]] = $quotes[1][2];
        }
        
        return $dict;
    }

    /**
     * Checks whether the given faction's briefing is locked.
     * Ignores user access level.
     *
     * @return boolean
     */
    public function briefingLocked($faction)
    {
        return $this->{'locked_' . strtolower($this->factions[$faction]) . '_briefing'} > 0;
    }

    /**
     * Gets mission mode and map details from name.
     * Validates mission names and aborts if invalid name.
     *
     * @return object
     */
    public static function getDetailsFromName($name)
    {
        if (substr(strtolower($name), -3) != 'pbo') {
            abort(400, 'Mission file must be a PBO');
            return;
        }

        if (strpos($name, '_') === false) {
            abort(400, 'Mission name must be in the format ARC_COOP/TVT/PREOP_Name_Author.Map');
            return;
        }

        $name = substr($name, 0, -4);
        $mapName = last(explode('.', $name));
        $parts = explode('_', rtrim($name, ".{$mapName}"));
        $map = Map::whereRaw('LOWER(class_name) = ?', [strtolower($mapName)])->first();

        if (is_null($map)) {
            $map = new Map();
            $map->display_name = $mapName;
            $map->class_name = $mapName;
            $map->save();
        }

        if (sizeof($parts) < 3) {
            abort(400, 'Mission name must be in the format ARC_COOP/TVT/PREOP_Name_Author.Map');
            return;
        }

        $group = $parts[0];
        $mode = strtolower($parts[1]);
        $validModes = ['coop', 'co', 'tvt', 'pvp', 'adv', 'preop'];

        if (in_array($mode, $validModes)) {
            if ($mode == 'co') {
                $mode = 'coop';
            }

            if (in_array($mode, ['tvt', 'pvp', 'adv'])) {
                $mode = 'adversarial';
            }
        } else {
            abort(400, 'Mission game mode is invalid. Must be one of COOP, TVT or PREOP');
            return;
        }

        $details = new stdClass();
        $details->mode = $mode;
        $details->map = $map;

        return $details;
    }

    /**
     * Gets the mission note notifications.
     *
     * @return Collection
     */
    public function noteNotifications()
    {
        $filtered = auth()->user()->unreadNotifications->filter(function ($item) {
            return
                $item->type == MissionNoteAdded::class &&
                $item->data['note']['mission_id'] == $this->id;
        });

        return $filtered;
    }

    /**
     * Gets the mission comments notifications.
     *
     * @return Collection
     */
    public function commentNotifications()
    {
        $filtered = auth()->user()->unreadNotifications->filter(function ($item) {
            return
                ($item->type == MissionCommentAdded::class &&
                $item->data['comment']['mission_id'] == $this->id) ||
                ($item->type == MentionedInComment::class &&
                $item->data['mission_id'] == $this->id);
        });

        return $filtered;
    }

    /**
     * Gets the mission verification notifications.
     *
     * @return Collection
     */
    public function verifiedNotifications()
    {
        $filtered = auth()->user()->unreadNotifications->filter(function ($item) {
            return
                $item->type == MissionVerified::class &&
                $item->data['mission']['id'] == $this->id;
        });

        return $filtered;
    }

    /**
     * Gets the mission updated/published notifications.
     *
     * @return Collection
     */
    public function stateNotifications()
    {
        $filtered = auth()->user()->unreadNotifications->filter(function ($item) {
            return
                ($item->type == MissionPublished::class &&
                $item->data['mission']['id'] == $this->id) ||
                ($item->type == MissionUpdated::class &&
                $item->data['mission_id'] == $this->id);
        });

        return $filtered;
    }
}
