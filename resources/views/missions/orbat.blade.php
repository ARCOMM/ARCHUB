@php
    $level = $mission->orbat($faction);
@endphp

<div class="float-start w-100 mt-3">
    @include('partials.orbat', $level)
</div>
