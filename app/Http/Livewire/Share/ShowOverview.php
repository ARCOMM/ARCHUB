<?php

namespace App\Http\Livewire\Share;

use App\Models\Missions\Mission;

use Livewire\Component;

class ShowOverview extends Component
{
    public Mission $mission;

    public function render()
    {
        return view('livewire.share.show-overview');
    }
}
