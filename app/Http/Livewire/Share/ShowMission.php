<?php

namespace App\Http\Livewire\Share;

use App\Models\Missions\Mission;

use Livewire\Component;

class ShowMission extends Component
{
    public Mission $mission;
    public $panel = 'overview';
    
    public function render() {
        return view('livewire.share.show-mission')
        ->extends('layouts.share');
    }
}
