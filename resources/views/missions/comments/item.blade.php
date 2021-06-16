@if ($comment->published)
    <div id="comment-{{ $comment->id }}" class="mission-comment-item">
        <span
            class="mission-comment-item-avatar"
            style="background-image: url({{ $comment->user->avatar }})">
        </span>

        <span class="mission-comment-item-username">
            {{ $comment->user->username }}
        </span>

        <span class="mission-comment-item-timestamp" title="{{ $comment->created_at }}">
            {{ $comment->created_at->diffForHumans() }}

            @if ($comment->isMine())
                &nbsp;&nbsp;&middot;

                <a href="javascript:void(0)" class="btn btn-sm m-a-0 mission-comment-control mission-comment-control-edit" data-id="{{ $comment->id }}" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>

                <a href="javascript:void(0)" class="btn btn-sm m-a-0 mission-comment-control mission-comment-control-delete" data-id="{{ $comment->id }}" title="Delete">
                    <i class="fa fa-times"></i>
                </a>
            @endif
        </span>

        <span class="mission-comment-item-text">
            {!! nl2br($comment->text) !!}
        </span>
    </div>
@endif
