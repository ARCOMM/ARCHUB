<tr class="operation-item {{ (!$operation->isNextToRun()) ?: 'table-active' }}" data-id="{{ $operation->id }}">
    <td>
        {{ $operation->starts_at->format('jS F') }}
    </td>

    <td>
        {{ $operation->starts_at->format('H:i') }}
    </td>

    @for ($i = 1; $i <= 3; $i++)
        @php
            $item = \App\Models\Operations\OperationMission::where('play_order', $i)->where('operation_id', $operation->id)->first();
        @endphp

        <td>
            @if (is_null($item))
                <a
                    href="javascript:void(0)"
                    class="operation-item-mission-item unassigned"
                    data-item="-1"
                    data-mission="-1"
                    data-order="{{ $i }}">
                    Assign Mission
                </a>
            @else
                <a
                    href="javascript:void(0)"
                    class="operation-item-mission-item assigned"
                    data-item="{{ $item->id }}"
                    data-mission="{{ $item->mission->id }}"
                    data-order="{{ $i }}">
                    <b>{{ $item->mission->display_name }}</b>
                </a>
            @endif
        </td>
    @endfor

    <td>
        <a href="javascript:void(0)" class="btn btn-primary float-end ms-1 oc-delete" data-id="{{ $operation->id }}">
            <i class="fa fa-trash"></i>
        </a>
    </td>
</tr>
