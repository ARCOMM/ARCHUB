@php
    use App\Models\Operations\Operation;
    use App\Models\Missions\Mission;
@endphp

@php
    $nextOperation = Operation::nextWeek();
    if ($nextOperation) {
        $nextOperationMissions = $nextOperation->missions;
    }
    $prevOperation = Operation::lastWeek();

    $myMissions = auth()->user()->missions();
    $newMissions = Mission::allNew();
    $pastMissions = Mission::allPast();
    $isTester = auth()->user()->can('test-missions');
@endphp

<script>
    $(document).ready(function(event) {
        $('#filter_select').select2({
            multiple: true,
            placeholder: "Tags",
            tags: true,
        });

        $.ajax({
            type: 'GET',
            url: '{{ url("/hub/missions/tags") }}',

            success: function(tagIds) {
                $.each(tagIds, function(index, value) {
                    var newOption = new Option(value["name"], index, false, false);
                    $('#filter_select').append(newOption).trigger('change');
                });
            }
        });

        function filter() {
            $.ajax({
                type: 'POST',
                url: "{{ url('/hub/missions/search') }}",
                data: {
                    tags: JSON.stringify($('#filter_select').select2('data'))
                },

                success: function(data) {
                    $('#filter_results').html(data);
                }
            });
        }

        document.getElementById("filter_btn").addEventListener("click", filter)
    });
</script>

<div class="missions-pinned">
    <div class="missions-pinned-groups">
        @if ($nextOperation)
            @if (!$nextOperationMissions->isEmpty())
                <ul class="mission-group mission-group-pinned" data-title="Next Operation &mdash; {{ $nextOperation->startsIn() }}">
                    @foreach ($nextOperationMissions as $item)
                        @include('missions.item', [
                            'mission' => $item->mission, 
                            'isTester' => $isTester, 
                            'classes' => 'mission-item-pinned', 
                            'pulse' => true
                        ])
                    @endforeach
                </ul>
            @else
                <div
                    class="mission-empty-group mission-group-pinned"
                    data-title="Next Operation &mdash; {{ $nextOperation->startsIn() }}"
                    data-subtitle="Missions haven't been picked yet!">
                </div>
            @endif
        @endif

        @if ($prevOperation)
            <ul class="mission-group mission-group-pinned" data-title="Past Operation">
                @foreach ($prevOperation->missions as $item)
                    @include('missions.item', ['mission' => $item->mission, 'isTester' => $isTester])
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="mission-tags">
    <select name="tags" class="form-control" id="filter_select"></select>
    <div class="text-center">
        <button type="button" class="btn btn-primary text-center" id="filter_btn">Filter</filter>
    </div>
</div>

<div id="filter_results">
    @include('missions.search')
</div>
