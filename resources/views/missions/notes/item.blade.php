<div id="note-{{ $note->id }}" class="mission-comment-item">
    <span
        class="mission-comment-item-avatar"
        style="background-image: url('{{ $note->user->avatar }}')">
    </span>

    <span class="mission-comment-item-username">
        {{ $note->user->username }}
    </span>

    <span class="mission-comment-item-timestamp">
        {{ $note->created_at->diffForHumans() }}

        @if ($note->isMine())
            &nbsp;&nbsp;&middot;

            <a href="javascript:void(0)" title="Delete" class="btn btn-sm mx-1 float-end mission-note-delete" data-id="{{ $note->id }}">
                <i class="fa fa-trash"></i>
            </a>

            <a href="javascript:void(0)" title="Edit" class="btn btn-sm mx-1 float-end mission-note-edit" data-id="{{ $note->id }}">
                <i class="fa fa-pencil"></i>
            </a>
        @endif
    </span>

    <span class="mission-comment-item-text">
        @markdown($note->text)
    </span>
</div>
