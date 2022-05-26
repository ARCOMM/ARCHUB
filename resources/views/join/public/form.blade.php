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
        <p class="bg-info">We're looking for well adjusted people who listen to and respect others. While a vast amount of experience in Arma is welcome, it's not required. Aspiring members should make an effort to attend the weekly operations, be accountable for their actions and generally have a good sense of humor.</p>

        <br />

        <form method="post" action="/join" id="join-form">
            <input type="hidden" name="source_text" id="f_source_text">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Your Name</label>

                        @if ($errors->first('name'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('name') }}</span>
                        @endif

                        <span class="help-block">This is the name you use in Arma and should be the same as on TeamSpeak.</span>
                        <input type="text" name="name" class="form-control" placeholder="Name" maxlength="255" value="{{ old('name') }}" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">How did you find out about us?</label>

                        @if ($errors->first('source_id'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('source_id') }}</span>
                        @endif

                        <span class="help-block">Knowing this helps our recruitment team.</span>
                        <select class="form-control" name="source_id" data-placeholder="Select" id="f_source_id">
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}" {{ (old('source_id') == $source->id) ? 'selected="true"' : '' }}>{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Your Age</label>

                        @if ($errors->first('age'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('age') }}</span>
                        @endif

                        <span class="help-block">Enter your current age in numeric digits.</span>
                        <input type="number" name="age" class="form-control" placeholder="Age" value="{{ old('age') }}" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Your Location</label>

                        @if ($errors->first('location'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('location') }}</span>
                        @endif

                        <span class="help-block">You can be as specific as you like.</span>
                        <input type="text" name="location" class="form-control" placeholder="Location" value="{{ old('location') }}" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Your Email Address</label>

                        @if ($errors->first('email'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('email') }}</span>
                        @endif

                        <span class="help-block">We use your email address to contact you about your application. You must enter a valid and regularly checked email address.</span>
                        <input type="text" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Your Steam Account</label>

                        @if ($errors->first('steam'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('steam') }}</span>
                        @endif

                        <span class="help-block">Please provide a link to your Steam account. You can do this by going to Steam > Your Name > Right-Click > Copy Page URL</span>
                        <input type="text" name="steam" class="form-control" placeholder="Steam Account" value="{{ old('steam') }}" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Are you available Saturdays at {{ config('app.op_time', '--:--') }} time?</label>

                        @if ($errors->first('available'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('available') }}</span>
                        @endif

                        <span class="help-block">This is when main operations take place.</span>
                        <select class="form-control" name="available" data-placeholder="Select">
                            <option value="0" {{ (old('available') == '0') ? 'selected="true"' : '' }}>No</option>
                            <option value="1" {{ (old('available') == '1') ? 'selected="true"' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Do you own the Apex expansion?</label>

                        @if ($errors->first('apex'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('apex') }}</span>
                        @endif

                        <span class="help-block">Owning it is not mandatory.</span>
                        <select class="form-control" name="apex" data-placeholder="Select">
                            <option value="0" {{ (old('apex') == '0') ? 'selected="true"' : '' }}>No</option>
                            <option value="1" {{ (old('apex') == '1') ? 'selected="true"' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Are you currently in another Arma group?</label>

                        @if ($errors->first('groups'))
                            <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('groups') }}</span>
                        @endif

                        <span class="help-block">This won't affect your chances of acceptance.</span>
                        <select class="form-control" name="groups" data-placeholder="Select">
                            <option value="0" {{ (old('groups') == '0') ? 'selected="true"' : '' }}>No</option>
                            <option value="1" {{ (old('groups') == '1') ? 'selected="true"' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">Your Arma Experience</label>

                @if ($errors->first('experience'))
                    <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('experience') }}</span>
                @endif
                
                <span class="help-block">Give us a short description of your Arma experience, what mods you've used etc.</span>
                <textarea name="experience" class="form-control" placeholder="Arma Experience" style="height:130px">{{ old('experience') }}</textarea>
            </div>

            <div class="form-group">
                <label class="control-label">About Yourself</label>

                @if ($errors->first('bio'))
                    <span class="help-block mt-2 mb-0 has-error">{{ $errors->first('bio') }}</span>
                @endif

                <span class="help-block" style="margin-bottom: 0;">Tell us a bit about yourself and how you think you would contribute as a member.</span>
                <span class="help-block" style="margin-top: 0;">
                    The more you write the better! This part is very important. Sharing a bit about yourself goes a long way. We're not looking for one-liners.
                </span>
                <textarea name="bio" class="form-control" placeholder="About Yourself" style="height:300px">{{ old('bio') }}</textarea>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" class="btn btn-primary btn-fill btn-dark float-end" value="Submit Application" />
            </div>
        </form>
    </div>
@endsection
