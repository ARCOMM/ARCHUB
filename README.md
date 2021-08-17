<p align="center">
	<img src="http://i.imgur.com/7nkLRBB.png">
</p>

<h1 align="center">Community Hub</h1>

### Site Features
- Full mobile support
- Built in notifications
- Discord authentication

![Screenshot 1](http://i.imgur.com/PHG3FFl.png)

### Mission Features
- Automated mission error checking
    - Checks configs for syntax errors
    - Checks loadout files for SQF syntax errors
    - Checks mission file is named correctly
    - Tells you which files at what lines are broken
- Mission weather reports
- Mission briefings
    - Choose to lock briefings
    - Shows the radio comm plan
- Mission media (photo, video)
- Download missions as PBO or ZIP
- After-Action Report
    - Save as draft to finish later
    - Create, edit and delete your posts
- Update missions with a new PBO, keeping existing media/comments
- Mission verification to ensure quality on the operation day
- Mission updates will be logged as revisions
- Discord notifications for basically everything

![Screenshot 2](http://i.imgur.com/zFwuBXU.png)

### Operation Features
- Create operations for the given date/time
- Assign missions to operations, in order

### User Features
- Sync avatar from Discord
- Change username
- Permissions system

![Screenshot 3](http://i.imgur.com/NpEGOeH.png)

### Application Features
- Sort apps into custom categories
- Send apps custom preset emails
- Filter apps by name, sort by date
- Blacklist certain apps by IP

### Requirements
- Recommended to use [Laravel Forge](https://forge.laravel.com) for automated setup
- Server needs [Google Cloud SDK](https://cloud.google.com/sdk/) installed
- Server needs [Armake](https://github.com/KoffeinFlummi/armake) installed
- Uses Google Cloud Storage
- Put Google Cloud Key in root directory named 'gcs.json'
