@if ($comment->published)
    <div id="comment-{{ $comment->id }}" class="mission-comment-item">
        <span
            class="mission-comment-item-avatar"
            style="background-image: url('{{ $comment->user->avatar }}')">
        </span>

        <span class="mission-comment-item-username">
            {{ $comment->user->username }}
        </span>

        <span class="mission-comment-item-timestamp" title="{{ $comment->created_at }}">
            {{ $comment->created_at->diffForHumans() }}

            @if ($comment->isMine())
                &nbsp;&nbsp;&middot;

                <a href="javascript:void(0)" title="Delete" class="btn btn-sm mx-1 float-end mission-comment-control mission-comment-control-delete" data-id="{{ $comment->id }}">
                    <i class="fa fa-trash"></i>
                </a>

                <a href="javascript:void(0)" title="Edit" class="btn btn-sm mx-1 float-end mission-comment-control mission-comment-control-edit" data-id="{{ $comment->id }}">
                    <i class="fa fa-pencil"></i>
                </a>
            @endif
        </span>

        <span class="mission-comment-item-text">
            @markdown($comment->text)
        </span>
    </div>
@endif
