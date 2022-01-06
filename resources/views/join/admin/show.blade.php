@extends('layout')

@section('title')
    {{ $jr->name }} &middot; Applications
@endsection

@section('header-color')
    primary
@endsection

@section('head')
@endsection

@section('content')
    <div class="container">
        <div class="card p-a-3">
            @can('manage-applications')
                <script>
                    $(document).ready(function(e) {
                        $('#send-app-email').click(function(event) {
                            $('#emailModal').modal('show');
                            event.preventDefault();
                        });

                        reloadSubmissions = function() {
                            $.ajax({
                                type: 'GET',
                                url: '{{ url('/hub/applications/api/email-submissions?jr_id='.$jr->id) }}',
                                success: function(data) {
                                    $('#email-submissions').html(data);
                                }
                            });
                        }
                    });
                </script>

                <button
                    class="btn btn-secondary pull-right"
                    type="button"
                    title="Choose an email to send"
                    id="send-app-email">
                    <i class="fa fa-paper-plane"></i>
                </button>

                <div class="modal fade" id="emailModal" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>

                                <h4 class="modal-title">Email {{ $jr->name }}</h4>
                            </div>

                            <div class="modal-body">
                                @include('join.admin.email-modal')
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="email-modal-send">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            @can('manage-applications')
                <div id="status" class="pull-right">
                    @include('join.admin.status', [
                        'joinStatuses' => $joinStatuses,
                        'jr' => $jr
                    ])
                </div>
            @endcan

            <h2>{{ $jr->name }}</h2>

            <table class="table table-sm no-border pull-left" style="margin: 15px 0 30px 0">
                <tr>
                    <td width="50%">
                        <i class="jr-icon fa fa-envelope"></i>
                        {{ $jr->email }}
                    </td>

                    <td width="50%" class="{{ ($jr->age) ? 'text-success' : 'text-danger' }}">
                        <i class="jr-icon fa fa-calendar"></i>
                        {{ $jr->age }}
                    </td>
                </tr>

                <tr>
                    <td>
                        <i class="jr-icon fa fa-map-marker"></i>
                        {{ $jr->location }}
                    </td>

                    <td>
                        <i class="jr-icon fa fa-steam-square"></i>
                        <a href="{{ $jr->steam }}" target="_newtab">Steam Account</a>
                    </td>
                </tr>

                <tr>
                    <td class="{{ ($jr->available) ? 'text-success' : 'text-danger' }}">
                        <i class="jr-icon fa fa-clock-o"></i>
                        {{ ($jr->available) ? 'Available ' : 'Unavailable ' }} {{ env('SITE_OP_DAY') }}
                    </td>

                    <td class="{{ ($jr->groups) ? 'text-danger' : 'text-success' }}">
                        <i class="jr-icon fa fa-users"></i>
                        {{ ($jr->groups) ? 'Other groups' : 'No other groups' }}
                    </td>
                </tr>

                <tr>
                    <td class="{{ ($jr->apex) ? 'text-success' : 'text-danger' }}">
                        <i class="jr-icon fa fa-gift"></i>
                        {{ ($jr->apex) ? 'Owns Apex' : 'Does not own Apex' }}
                    </td>

                    <td>
                        <i class="jr-icon fa fa-globe"></i>
                        {{ $jr->source->name }}
                        @if (strlen($jr->source_text))
                            ({{ $jr->source_text }})
                        @endif
                    </td>
                </tr>
            </table>

            <h5 class="m-t-3">Arma Experience</h5>
            <p>{!! $jr->experience !!}</p>

            <h5 class="m-t-3">About</h5>
            <p class="m-b-0">{!! $jr->bio !!}</p>
        </div>

        <div class="card p-a-3">
            <h4>Emails</h4>

            <div class="list-group m-b-0 p-b-0" id="email-submissions">
                @include('join.admin.email-submissions')
            </div>
        </div>
    </div>
@endsection
