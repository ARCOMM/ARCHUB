@php
    use App\Models\Operations\Operation;
@endphp

@php
    $nextOperation = Operation::nextWeek();
    if ($nextOperation) {
        $nextOperationMissions = $nextOperation->missions;
    }
    $prevOperation = Operation::lastWeek();
    $isTester = auth()->user()->can('test-missions');
@endphp

<script>
    $(document).ready(function(event) {
        $('#mode_select').select2({
            placeholder: "Mode",
            dropdownParent: $('#filter_modal'),
            allowClear: true,
        });

        $.ajax({
            type: 'GET',
            url: '{{ url("/hub/missions/modes") }}',

            success: function(modes) {
                $.each(modes, function(index, value) {
                    var newOption = new Option(value, index, false, false);
                    $('#mode_select').append(newOption);
                });
                $('#mode_select').val(null).trigger('change'); // Ensure selection starts empty
            }
        });

        $('#author_select').select2({
            placeholder: "Mission maker",
            dropdownParent: $('#filter_modal'),
            allowClear: true,
            ajax: {
                delay: 250,
                type: 'GET',
                url: '{{ url("/hub/users/search") }}',
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $('#whitelist_select').select2({
            multiple: true,
            placeholder: "Whitelist",
            dropdownParent: $('#filter_modal')
        });

        $('#blacklist_select').select2({
            multiple: true,
            placeholder: "Blacklist",
            dropdownParent: $('#filter_modal')
        });

        $.ajax({
            type: 'GET',
            url: '{{ url("/hub/missions/tags") }}',

            success: function(tagIds) {
                $.each(tagIds, function(index, value) {
                    var newOption = new Option(value["name"], index, false, false);
                    var newOption2 = new Option(value["name"], index, false, false);
                    $('#whitelist_select').append(newOption).trigger('change');
                    $('#blacklist_select').append(newOption2).trigger('change');
                });
            }
        });

        function filter() {
            var mode = $('#mode_select').select2('data');
            var author = $('#author_select').select2('data');

            $.ajax({
                type: 'GET',
                url: "{{ url('/hub/missions/search') }}",
                data: {
                    "mode": mode.length > 0 ? mode[0]["text"] : null,
                    "author_id": author.length > 0 ? author[0]["id"] : null,
                    "whitelist[]": $('#whitelist_select').select2('data').map(item => item.text),
                    "blacklist[]": $('#blacklist_select').select2('data').map(item => item.text),
                },

                success: function(data) {
                    $('#filter_results').html(data);
                }
            });
        }

        function clear() {
            $('#mode_select').val(null).trigger('change');
            $('#author_select').val(null).trigger('change');
            $('#whitelist_select').val(null).trigger('change');
            $('#blacklist_select').val(null).trigger('change');
            filter();
        }

        document.getElementById("filter_btn").addEventListener("click", filter)
        document.getElementById("clear_btn").addEventListener("click", clear)
    });
</script>

@can('manage-operations')
    @include('missions.operations')
@endcan

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

<div class="filter-div me-auto">
    <button type="button" class="btn btn-primary btn-raised float-end" data-bs-toggle="modal" data-bs-target="#filter_modal">
        Filter
    </button>
</div>

<div class="modal" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="mission-tags">
                    <select name="mode" class="form-control" style="width: 100%" id="mode_select"></select>
                    <select name="author" class="form-control" style="width: 100%" id="author_select"></select>
                    <select name="whitelist" class="form-control" style="width: 100%" id="whitelist_select"></select>
                    <select name="blacklist" class="form-control" style="width: 100%" id="blacklist_select"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="clear_btn">Clear</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="filter_btn">Apply</button>
            </div>
        </div>
    </div>
</div>

<div id="filter_results">
    @include('missions.search')
</div>
