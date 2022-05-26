@extends('layout')

@section('title')
    Applications
@endsection

@section('header-color')
    primary
@endsection

@section('head')
    <script>
        $(document).ready(function(e) {
            window.jr = {
                status: "pending"
            };

            loadItems = function(status, order, callback) {
                $.ajax({
                    type: 'GET',
                    url: '{{ url('/hub/applications/api/items') }}?status=' + status + '&order=' + order,
                    success: function(data) {
                        if (typeof callback == "function")
                            callback(data);
                    }
                });
            }

            $('.status-filter').click(function(event) {
                var $this = $(this);
                var status = $(this).data("status");
                window.jr.status = status;

                $('.subnav-link').removeClass("active");
                $this.addClass("active");
                setUrl('hub/applications/' + status);

                loadItems(status, "desc", function(data) {
                    $('#join-requests').html(data);
                    $('#search').val("");
                    $('.content').scrollTop(0);
                    $('#join-filter-form').show();
                });

                event.preventDefault();
            });

            $('#search').keyup(function(event) {
                var query = $(this).val().toLowerCase();

                $('.jr-item').each(function(i, e) {
                    var name = $(e).find('.jr-item-title').html().toLowerCase();

                    if (name.includes(query)) {
                        $(e).show();
                    } else {
                        $(e).hide();
                    }
                });
            });

            $('#order').change(function(event) {
                var order = $(this).val();
                loadItems(window.jr.status, order, function(data) {
                    $('#join-requests').html(data);
                });
            });
        });
    </script>
@endsection

@section('subnav')
    @foreach ($joinStatuses as $status)
        <a
            href="javascript:void(0)"
            data-status="{{ $status->permalink }}"
            class="subnav-link status-filter {{ (request()->segment(3) == $status->permalink) ? 'active' : '' }}">
            {{ $status->text }} &middot; {{ $status->count() }}
        </a>
    @endforeach

    @can('manage-applications')
        <script>
            $(document).ready(function(e) {
                reloadEmails = function() {
                    $.ajax({
                        type: 'GET',
                        url: '{{ url('/hub/applications/api/emails') }}',
                        success: function(data) {
                            $('#join-requests').html(data);
                            $('#join-filter-form').hide();
                        }
                    });
                }

                $('#app-emails').click(function(event) {
                    reloadEmails();
                    $('.subnav-link').removeClass("active");
                    $(this).addClass("active");
                    event.preventDefault();
                });
            });
        </script>

        <a href="javascript:void(0)" class="subnav-link" id="app-emails">Email Templates</a>
    @endcan
@endsection

@section('content')
    <div class="container">
        <form method="post" id="join-filter-form" class="form-inline mb-3" style="text-align: right">
            <div class="form-group">
                <input type="text" name="search" id="search" class="form-control" placeholder="Search" style="width:250px">
            </div>

            {{-- <div class="form-group" style="height: 71px">
                <select name="order" class="form-control" id="order" style="width:150px">
                    <option value="desc">Latest first</option>
                    <option value="asc">Oldest first</option>
                </select>
            </div> --}}
        </form>

        <div class="card" id="join-requests">
            @include('join.admin.items')
        </div>
    </div>
@endsection
