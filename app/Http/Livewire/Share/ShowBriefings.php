<?php

namespace App\Http\Livewire\Share;

use App\Models\Missions\Mission;

use Livewire\Component;

class ShowBriefings extends Component
{
    public Mission $mission;
    public $factions;
    public $activeFaction;

    public function mount() {
        $this->factions = $this->mission->briefingFactions();
        $this->activeFaction = $this->factions[0]->faction;
    }

    public function render()
    {
        $this->factions = $this->mission->briefingFactions();
        
        return view('livewire.share.show-briefings');
    }
}
