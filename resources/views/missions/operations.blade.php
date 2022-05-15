<script>
    $(document).ready(function(e) {
        var slot = null;

        $(document).on('click', '.operation-item-mission-item', function(event) {
            var caller = $(this);
            var isAssigned = caller.hasClass('assigned');

            if (isAssigned) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url('/hub/missions/operations/remove-mission') }}',
                    data: {'id': caller.data('item') || -1},
                    success: function(data) {
                        caller.html('Assign Mission');
                        caller.removeClass('assigned');
                        caller.addClass('unassigned');
                    }
                });

                return;
            }

            $('.operation-item-mission-item.unassigned').html('Assign Mission');

            caller.html('Pick a mission below');
            slot = caller;

            $('.operations-mission-browser').removeClass('hide');

            event.preventDefault();
        });

        $('.mission-item').click(function(event) {
            event.preventDefault();

            var caller = $(this);
            var mission_id = caller.data('id');
            var operation_id = slot.parents('.operation-item').data('id');
            var order = slot.data('order');

            if (slot != null) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url('/hub/missions/operations/add-mission') }}',
                    data: {
                        'mission_id': mission_id,
                        'operation_id': operation_id,
                        'play_order': order
                    },
                    success: function(data) {
                        slot.data('mission', mission_id);
                        slot.data('item', data.trim());
                        slot.html('<b>' + caller.find('.mission-item-title').html() + '</b>');
                        slot.removeClass('unassigned');
                        slot.addClass('assigned');
                        $('.operations-mission-browser').addClass('hide');
                    }
                });
            }
        });

        $('#create-operation-form').submit(function(event) {
            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/missions/operations/create-operation') }}',
                data: $('#create-operation-form').serialize(),
                success: function(data) {
                    $('.operation-rows').prepend(data);
                }
            });

            event.preventDefault();
        });

        $(document).on('click', '.oc-delete', function(event) {
            var caller = $(this);

            $.ajax({
                type: 'POST',
                url: '{{ url('/hub/missions/operations/delete-operation') }}',
                data: {operation_id: caller.data('id')},
                success: function(data) {
                    caller.parents('.operation-item').remove();
                }
            });

            event.preventDefault();
        });
    });
</script>

@php
    use App\Models\Operations\Operation;
@endphp

<div class="large-panel-content full-page mb-5">
    <div class="pull-left w-100">
        <form id="create-operation-form" class="pull-right">
            <div class="row">
                <div class="col">
                    <input type="datetime-local" class="form-control" name="starts_at">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-raised btn-primary mb-0 mt-1">Create Operation</button>
                </div>
            </div>
        </form>
    </div>

    <div class="operations">
        <table class="table">
            <thead>
                <tr>
                    <th width="150">Date</th>
                    <th width="100">Time</th>
                    <th>First</th>
                    <th>Second</th>
                    <th>Third</th>
                    <th width="100">&nbsp;</th>
                </tr>
            </thead>

            <tbody class="operation-rows">
                @foreach (Operation::orderBy('starts_at', 'desc')->take(6)->get() as $operation)
                    @include('missions.operations.item', ['operation' => $operation])
                @endforeach
            </tbody>
        </table>
    </div>
</div>
