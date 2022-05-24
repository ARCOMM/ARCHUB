<?php

namespace App\Models\Missions;

use Exception;
use \stdClass;
use Carbon\Carbon;
use App\Helpers\ArmaConfigError;
use App\Helpers\PBOMission\PBOMission;
use App\Models\Portal\User;
use App\Models\Tags\MissionTag;
use App\Models\Operations\OperationMission;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Mission extends Model implements HasMedia
{
    use Notifiable;
    use InteractsWithMedia;

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
     * The addons that should be warned from using.
     *
     * @var array
     */
    public $addonWarnings = [
        'rhs',
        'cfp'
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
    public static $gamemodes = [
        'coop' => 'Cooperative',
        'adversarial' => 'Adversarial',
        'arcade' => 'Arcade'
    ];

    /**
     * Side represented as an integer as used by TMF_OrbatSettings
     * From: https://community.bistudio.com/wiki/BIS_fnc_sideID
     *
     * @var array
     */
    public static $sideMap = [
        "opfor" => 0,
        "east" => 0,
        "blufor" => 1,
        "west" => 1,
        "independent" => 2,
        "resistance" => 2,
        "civilian" => 3
    ];

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Media library image conversions.
     *
     * @return void
     */
    public function registerMediaConversions(Media $media = null): void
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
     * Gets all past missions (last played is in past and not null).
     *
     * @return Collection App\Models\Missions\Mission
     */
    public static function allPast()
    {
        return Mission::with('user')->with('map')
            ->where('last_played', '!=', null)
            ->where('last_played', '<', Carbon::now()->toDateTimeString())
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
        return Mission::with('user')->with('map')
            ->where('last_played', null)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Checks whether the mission is new.
     *
     * @return boolean
     */
    public function hasBeenPlayed()
    {
        return !is_null($this->last_played);
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

    public function tags()
    {
        return $this->hasMany(MissionTag::class);
    }

    /**
     * Gets the mission banner URL.
     *
     * @return string
     */
    public function banner()
    {
        $media = $this->photos();

        if (count($media) > 0) {
            return $media[0]->getUrl();
        }
        
        return '';
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
        if ($this->isLegacy()) {
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

        return url('/images/arcomm-placeholder.jpg');
    }

    /**
     * Gets a full text string of the game mode.
     *
     * @return string
     */
    public function fulltextGamemode()
    {
        return self::$gamemodes[strtolower($this->mode)];
    }

    /**
     * Gets the exported name of the file following the mission name format.
     *
     * @return string
     */
    public function exportedName()
    {
        $revisions = $this->revisions()->count();
        $filename = pathinfo($this->original["file_name"], PATHINFO_FILENAME);
        [$filenameNoMap, $map] = explode('.', $filename, 2);

        return "{$filenameNoMap}_{$revisions}.{$map}.pbo";
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
     * Creates the downloadable file and returns its full URL.
     *
     * @return string
     */
    public function download()
    {
        return Storage::cloud()->temporaryUrl($this->cloud_pbo, now()->addMinutes(10));
    }

    /**
     * Gets the missions weather
     *
     * @return object
     */
    public function missionWeather()
    {
        return json_decode($this->weather, true);
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

    public function getBriefings()
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
            if (Str::startsWith(strtolower($addonName), strtolower($key))) {
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
    public function storeConfigs($path)
    {
        //parse mission.sqm
        $mission = new PBOMission(storage_path("app/{$path}"));
        $contents = $mission->export();

        if ($mission->error) {
            return new ArmaConfigError($mission->errorReason);
        }

        $validationError = $this->validateMissionContents($contents);
        if (!is_null($validationError)) {
            return new ArmaConfigError($validationError);
        }

        $this->display_name = $contents['mission']['name'];
        if (array_key_exists('description', $contents['mission'])) {
            $this->summary = $contents['mission']['description'];
        }

        $briefingsArray = $this->parseBriefings($contents['mission']['briefings']);
        $this->briefings = json_encode($briefingsArray);
        $this->dependencies = json_encode($contents['mission']['dependencies']);
        try {
            $this->orbatSettings = json_encode($this->orbatFromOrbatSettings($contents['mission']['orbatSettings'], $contents['mission']['groups']));
        } catch (Exception $e) {
            $this->orbatSettings = json_encode(array("Error extracting ORBAT:" => array($e->getMessage())));
        }
        $this->slottingDetails = $contents['mission']['slottingDetails'];

        if (array_key_exists('date', $contents['mission'])) {
            $this->date = $contents['mission']['date'];
        }
        if (array_key_exists('time', $contents['mission'])) {
            $this->time = $contents['mission']['time'];
        }
        $this->weather = json_encode($contents['mission']['weather']);

        $this->save();
        $this->deployCloudFiles($path);

        return $this;
    }

    private function validateMissionContents($contents)
    {
        $errorText = "";
        $files = $contents['pbo']['files'];

        $definedBriefings = $contents['mission']['briefings'];

        foreach ($definedBriefings as $briefing) {
            $briefingPath = $briefing[2];

            if (!array_key_exists($briefingPath, $files)) {
                $errorText .= sprintf("%s is defined in TMF but has no file\n", $briefing[0]);
            }
        }

        $expectedFiles = ["mission.sqm", "description.ext"];

        foreach ($expectedFiles as $file) {
            if (!array_key_exists($file, $files)) {
                $errorText .= sprintf("%s is missing\n", $file);
            }
        }

        if (strlen($errorText) > 0) {
            return $errorText;
        }
        return null;
    }

    /**
     * Deploys the mission files to cloud storage.
     * - PBO Download
     *
     * @return any
     */
    public function deployCloudFiles($path)
    {
        $qualified_pbo = "missions/{$this->user_id}/{$this->id}/{$this->exportedName()}";

        // Mission PBO
        Storage::cloud()->put(
            $qualified_pbo,
            file_get_contents(storage_path("app/{$path}"))
        );

        $this->cloud_pbo = $qualified_pbo;
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
        $forecastRain = $this->missionWeather()['forecast']['rain'] ?? 0;

        return static::computeLessThan(
            $forecastRain,
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
        return $this->overcast() . 
        (($this->fog() == '') ? '' : ', ' . 
        $this->fog()) . 
        (($this->rain() == '') ? '' : ', ' . 
        $this->rain());
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
        $briefings = $this->getBriefings();

        if ($briefings != null) {
            foreach ($briefings as $briefing) {
                // Skip briefings that aren't assigned to a faction
                if (!empty($briefing[1])) {
                    $factionId = $this->parseFactionId($briefing[1][0]);
                    $locked = $this->{'locked_'.strtolower($this->factions[$factionId]).'_briefing'};

                    if ($locked == 0 || (!auth()->guest() && ($this->isMine() || auth()->user()->can('test-missions')))) {
                        $nav = new stdClass();
                        $nav->name = $briefing[0];
                        $nav->faction = $factionId;
                        $nav->locked = $locked;

                        array_push($filledFactions, $nav);
                    }
                }
            }
        }

        return $filledFactions;
    }

    private function parseFactionId($rawFaction)
    {
        //Briefings are per faction numbers
        if (is_int($rawFaction)) {
            return $rawFaction;
        }

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
        $briefing = $this->getBriefing($factionId);
        $contents = $briefing[3];

        foreach ($contents as $name => $section) {
            $formattedSection = new stdClass();
            $formattedSection->title = $name;
            $formattedSection->paragraphs = $this->formatParagraphs($section);
            $formattedSection->locked = $this->{'locked_' . $factionId . '_briefing'};
            array_push($filledSubjects, $formattedSection);
        }

        return $filledSubjects;
    }

    private function getBriefing($factionId)
    {
        $availableBriefings = $this->getBriefings();
        foreach ($availableBriefings as $briefing) {
            $id = $this->parseFactionId($briefing[1][0]);
            if ($id == $factionId) {
                return $briefing;
            }
        }
        
        throw new Exception("Faction does not have a briefing file specified");
    }

    private function formatParagraphs($paragraphs)
    {
        return preg_replace("~<font(.*?)>(.*?)<\/font>~", "<b>$2</b>", $paragraphs);
    }

    private function parseBriefings($briefings)
    {
        for ($i = 0; $i < count($briefings); $i++) {
            $briefings[$i][3] = $this->parseBriefing($briefings[$i][3]);
        }

        return $briefings;
    }

    private function parseBriefing($briefing)
    {
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
            abort(400, 'Mission name must be in the format ARC_COOP/TVT/ADE_Name_Author.Map');
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
            abort(400, 'Mission name must be in the format ARC_COOP/TVT/ADE_Name_Author.Map');
            return;
        }

        $group = $parts[0];
        $mode = strtolower($parts[1]);

        $mode = match ($mode) {
            'ade' => 'arcade',
            'coop' => 'coop',
            'tvt' => 'adversarial',
            default => abort(400, 'Mission game mode is invalid. Must be one of COOP, TVT or ADE'),
        };

        $details = new stdClass();
        $details->mode = $mode;
        $details->map = $map;

        return $details;
    }

    /**
     * Returns the mission's orbat
     *
     * @return array
     */
    public function getOrbat()
    {
        return (array)json_decode($this->orbatSettings);
    }

    public function orbat($faction)
    {
        $orbat = $this->getOrbat();
        if (isset($orbat[$faction])) {
            return (array)$orbat[$faction];
        }

        return (array)array();
    }

    public function orbatFactions()
    {
        $orbat = $this->getOrbat();
        if (is_null($orbat)) {
            return array();
        }

        return array_keys($orbat);
    }

    /**
     * Returns a minimal version of orbatSettings for displaying on the website
     *
     * @return array
     */
    private function orbatFromOrbatSettings(array $orbats, array &$groups)
    {
        foreach ($orbats as &$faction) {
            $this->minimiseLevel($faction[1]);
            $faction = $faction[1];
        }

        $orbatGroups = array();
        foreach ($groups as &$group) {
            if (isset($group['orbatParent'])) {
                $faction = self::$sideMap[$group['side']];
                $orbatParent = $group['orbatParent'];
                $minimalGroup = array(isset($group['name']) ? $group['name'] : "NOT NAMED", array());

                foreach ($group['units'] as $unit) {
                    $desc = explode("@", $unit['description'])[0];
                    array_push($minimalGroup[1], array($desc));
                }

                if (!isset($orbatGroups[$faction])) {
                    $orbatGroups[$faction] = array();
                }

                if (!isset($orbatGroups[$faction][$orbatParent])) {
                    $orbatGroups[$faction][$orbatParent] = array($minimalGroup);
                } else {
                    array_push($orbatGroups[$faction][$orbatParent], $minimalGroup);
                }
            }
        }

        foreach ($orbats as $faction => &$orbat) {
            if (!is_array($orbat)) {
                unset($orbats[$faction]);
                continue;
            }

            $orbatModified = false;
            $this->replaceIntWithUnits($orbat[1], $faction, $orbatGroups, $orbatModified);

            if (!$orbatModified) {
                unset($orbats[$faction]);
            }
        }

        $this->removeAllIntsAndEmptyArrays($orbats);
        // Reindex array after everything has been unset
        $orbats = array_map('array_values', $orbats);

        $namedOrbats = array();
        foreach ($orbats as $faction => &$orbat) {
            $factionName = array_search($faction, self::$sideMap);
            $namedOrbats[$factionName] = &$orbat;
        }

        return $namedOrbats;
    }

    /**
     * Recursive function used by $this->orbatFromOrbatSettings()
     */
    private function minimiseLevel(array &$level)
    {
        $name = strlen($level[0][1]) > 0 ? $level[0][1] : $level[0][4]; //If an abbrievation is defined then use it, otherwise full name
        $uniqueId = $level[0][0];
        $level[0] = $name;

        if (count($level[1]) === 0) {
            $level[1] = array($uniqueId);
        } else {
            foreach ($level[1] as $i => &$subLevel) {
                $this->minimiseLevel($subLevel);
            }
            array_unshift($level[1], $uniqueId);
        }
    }

    private function replaceIntWithUnits(array &$item, int &$faction, array &$orbatGroups, bool &$orbatModified)
    {
        if (is_int($item[0])) {
            if (isset($orbatGroups[$faction][$item[0]])) {
                $units = &$orbatGroups[$faction][$item[0]];
                unset($item[0]);
                
                $units = array_reverse($units);
                
                foreach ($units as $unit) {
                    array_unshift($item, $unit);
                }
                $orbatModified = true;
            } else {
                unset($item[0]);
            }
        }

        foreach ($item as &$subitem) {
            if (is_array($subitem)) {
                $this->replaceIntWithUnits($subitem, $faction, $orbatGroups, $orbatModified);
            }
        }
    }

    private function removeAllIntsAndEmptyArrays(array &$item)
    {
        // Only have name + empty array - remove entire item
        if (isset($item[1]) && is_array($item[1]) && empty($item[1])) {
            $item = null;
            return;
        }

        // Recursively find every int and empty array and replace them will NULL
        foreach ($item as &$subitem) {
            if (is_int($subitem)) {
                $subitem = null;
            } elseif (is_array($subitem)) {
                if (empty($subitem)) {
                    $subitem = null;
                } else {
                    $this->removeAllIntsAndEmptyArrays($subitem);
                }
            }
        }

        // Double check if array is empty now that all the children have been unset
        if (isset($item[1]) && is_array($item[1]) && empty($item[1])) {
            $item = null;
            return;
        }

        // unset() used on a &reference will unset the reference but not the original value
        // instead we set everything to NULL and use array_filter, which will unset the original value
        $item = array_filter($item);
    }
}
