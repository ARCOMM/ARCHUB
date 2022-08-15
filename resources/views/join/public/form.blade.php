@extends('layout-public')

@section('title')
    Join
@endsection

@section('scripts')
    <script>
        $(document).ready(function(event) {
            $('select').select2({
                minimumResultsForSearch: -1
            });

            $('#f_source_id').change(function(event) {
                var id = $(this).val();

                // If friend suggestion
                if (id == 7) {
                    var friend = prompt("Name of the friend who suggested us?", "");
                    if (friend != null && friend != "") {
                        $('#f_source_text').val(friend);
                    }
                }

                event.preventDefault();
            });
        });
    </script>
@endsection

@section('content')
    <div class="content container">
        <h2>What makes a good member?</h2>
        <p class="bg-info">We're looking for well adjusted people who listen to and respect others. While having experience in our games is welcome, it's in no way required. Aspiring members should make an effort to attend the weekly operations, be accountable for their actions, and generally have a good sense of humor.</p>

        <br />

        <form method="post" action="/join" id="join-form">
            <input type="hidden" name="source_text" id="f_source_text">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="control-label">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" />

                        @error('name')
                            <div class="has-error">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted">This should be the name you use in game</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">How did you hear about us?</label>

                        <select class="form-control" name="source_id" data-placeholder="Select" id="f_source_id">
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}" {{ (old('source_id') == $source->id) ? 'selected="true"' : '' }}>{{ $source->name }}</option>
                            @endforeach
                        </select>

                        @error('source_id')
                            <div class="has-error">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted">Knowing this helps our recruitment team</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="age" class="control-label">Your Age</label>
                        <input type="number" id="age" name="age" class="form-control" placeholder="Age" value="{{ old('age') }}" />

                        @error('age')
                            <div class="has-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location" class="control-label">Your Location</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="Location" value="{{ old('location') }}" />

                        @error('location')
                            <div class="has-error">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted">You can be as specific as you like</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="discord" class="control-label">Your Discord Account</label>
                        <input type="text" id="discord" name="discord" class="form-control" placeholder="username#1234" value="{{ old('discord') }}" />

                        @error('discord')
                            <div class="has-error">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted">Please join <a href="{{ config('services.discord.invite_link') }}" target="_newtab">our Discord server</a> so that we can contact you about your application</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="steam" class="control-label">Your Steam Account</label>
                        <input type="text" id="steam" name="steam" class="form-control" placeholder="Steam Account" value="{{ old('steam') }}" />
                        
                        @error('steam')
                            <div class="has-error">{{ $message }}</div>
                        @enderror
                        
                        <small class="form-text text-muted">Please provide a link to your Steam account. You can do this by going to Steam > Your Name > Right-Click > Copy Page URL</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Are you available for weekly operations?</label>

                        <select class="form-control" name="available" data-placeholder="Select">
                            <option value="0" {{ (old('available') == '0') ? 'selected="true"' : '' }}>No</option>
                            <option value="1" {{ (old('available') == '1') ? 'selected="true"' : '' }}>Yes</option>
                        </select>

                        @error('available')
                            <div class="has-error">{{ $message }}</div>
                        @enderror

                        <small class="form-text text-muted">Arma - Saturday at {{config('app.op_time')}}</small>
                        </br>
                        <small class="form-text text-muted">DCS - Sunday at {{config('app.op_time')}}</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="experience" class="control-label">Your Game Experience</label>
                <textarea id="experience" name="experience" class="form-control" placeholder="Game Experience" style="height:130px">{{ old('experience') }}</textarea>
                
                @error('experience')
                    <div class="has-error">{{ $message }}</div>
                @enderror
                
                <small class="form-text text-muted">Give us a short description of your experience with the game, what mods you've used etc</small>
            </div>

            <div class="form-group">
                <label for="bio" class="control-label">About Yourself</label>
                <textarea id="bio" name="bio" class="form-control" placeholder="About Yourself" style="height:200px">{{ old('bio') }}</textarea>
                
                @error('bio')
                    <div class="has-error">{{ $message }}</div>
                @enderror
                
                <small class="form-text text-muted" style="margin-bottom:0;">Tell us a bit about yourself and how you think you would contribute as a member</small>
                </br>
                <small class="form-text text-muted" style="margin-top:0;">
                    The more you write the better! This part is very important. Sharing a bit about yourself goes a long way - we're not looking for one-liners
                </small>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" class="btn btn-primary btn-fill btn-dark float-end" value="Submit Application" />
            </div>
        </form>
    </div>
@endsection
