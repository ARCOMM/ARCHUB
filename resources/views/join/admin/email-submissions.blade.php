@if ($emailSubmissions->isEmpty())
    <p class="m-b-0">No emails have been sent yet.</p>
@else
    @foreach ($emailSubmissions as $sent)
        <li class="list-group-item jr-item p-x-0 p-b-0-lot">
            <span class="jr-item-title">{{ $sent->subject }}</span>
            <br />

            <span class="jr-item-meta">
                Sent by {{ $sent->user->name }} {{ $sent->created_at->diffForHumans() }}
            </span>
        </li>
    @endforeach
@endif
