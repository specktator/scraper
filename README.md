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

## ToDo List:
* instant answer box design
* duckduckgo class accept json flat lists format
* album art fallback (source: duckduckgo instant answer api)
* scrapers interface
* scraper scan for image file album cover
* track playback stats (times inserted in playlists, favorites, times played)
* image compression before storing
* audio files caching

## ToDo bugs list:
* volume slider margin -10
* overlay share btn tooltip positioning
* scraper 1st folder get files
* album cover smaller than default size breaks element floating
* queue playlist events stacking upon updating sortable
