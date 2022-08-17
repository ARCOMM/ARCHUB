@extends('layouts.public')

@section('title')
    Roster
@endsection

@section('scripts')
    <script>
        $(document).ready(function(event) {});
    </script>
@endsection

@section('content')
    <div class="content container">
        <h2>Roster &mdash; {{ count($members) }} Members</h2>
        @foreach ($members as $member)
            <div class="roster-item">
                <div class="ri-avatar" style="background-image: url('{{ $member->avatar }}')"></div>
                <div class="ri-name">{{ $member->username }}</div>
            </div>
        @endforeach
    </div>
@endsection
