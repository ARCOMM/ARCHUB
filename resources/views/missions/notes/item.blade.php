<div id="note-{{ $note->id }}" class="mission-comment-item">
    <span
        class="mission-comment-item-avatar"
        style="background-image: url({{ $note->user->avatar }})">
    </span>

    <span class="mission-comment-item-username">
        {{ $note->user->username }}
    </span>

    <span class="mission-comment-item-timestamp">
        {{ $note->created_at->diffForHumans() }}

        @if ($note->isMine())
            &nbsp;&nbsp;&middot;

            <a href="javascript:void(0)" title="Delete note" class="btn btn-sm m-a-0 mission-note-delete" data-id="{{ $note->id }}">
                <i class="fa fa-trash"></i>
            </a>
        @endif
    </span>

    <span class="mission-comment-item-text">
        {!! nl2br($note->text) !!}
    </span>
</div>
