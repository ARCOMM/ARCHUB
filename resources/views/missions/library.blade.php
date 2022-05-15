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
        $('#author_select').select2({
            placeholder: "Mission maker",
            dropdownParent: $('#filter_modal'),
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

        $('#filter_select').select2({
            multiple: true,
            placeholder: "Tags",
            dropdownParent: $('#filter_modal')
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
            var author = $('#author_select').select2('data')
            $.ajax({
                type: 'GET',
                url: "{{ url('/hub/missions/search') }}",
                data: {
                    "author": author.length > 0 ? author[0]["text"] : null,
                    "tags[]": $('#filter_select').select2('data').map(item => item.text)
                },

                success: function(data) {
                    $('#filter_results').html(data);
                }
            });
        }

        function clear() {
            $('#author_select').val(null).trigger('change');
            $('#filter_select').val(null).trigger('change');
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

<div class="filter-div">
    <button type="button" class="btn btn-primary btn-raised float-right" data-toggle="modal" data-target="#filter_modal">
        Filter
    </button>
</div>

<div class="modal" id="filter_modal" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="mission-tags">
                    <select name="author" class="form-control" style="width: 100%" id="author_select"></select>
                    <select name="tags" class="form-control" style="width: 100%" id="filter_select"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="clear_btn">Clear</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="filter_btn">Apply</button>
            </div>
        </div>
    </div>
</div>

<div id="filter_results">
    @include('missions.search')
</div>
