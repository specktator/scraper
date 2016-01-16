# scraper
apache indexes scraper - music player with id3 tags and duckduckgo instant answers for the audio content

##Features:
* Playing audio tracks directly from apache indexes local folders or other sources
* Portable, database-less
* Populate media library from apache indexes (for now)
* ID3 tags: extraction from local path, remote url, binary data and load via ajax in json format
* Extract album covers from ID3 tags and cache them, discover if album cover will be used for multiple tracks and use the cached one.
* Retrieve artist or track description via duckduckgo.com search engine and display it in player's OSD
* Search for media files
* Queue playlist and saved playlists
* Track playback stats, favorites, social media sharing (player/track/playlist embeded)
* Filter tracks by **artist name** and **genre**
* Album image compression before storing

## Features ToDo List:
* instant answer box design
* duckduckgo class accept json flat lists format
* duckduckgo class should cache the information 
* album art fallback (source: duckduckgo instant answer api)
* scrapers interface
* scraper scan for image file album cover
* track playback stats (times inserted in playlists, favorites, times played)
* audio files caching
* Hotkeys

## ToDo bugs list:
* volume slider margin -10
* overlay share btn tooltip positioning
* scraper 1st folder get files
* album cover smaller than default size breaks element floating
* queue playlist events stacking upon updating sortable
* streaming a file to Chrome cause seeking not to work

## temp todo list:
* create new playlist button
* rename playlist UI -> rename playlist function
* add to playlist UI -> update playlist function
* code design issue: queue playlist should be global and not affected by playlist update function, also updating playlist must not affect artsts songs list and genres songs list.
* context menu
* upon loadPlaylist should search for the currently playing track and make it active, plus syncronizing/toggling its play button
* if follow option is ON then playlist should scroll like main library