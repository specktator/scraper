<?php
/*

Copyright 2015 Christos Dimas <specktator@totallynoob.com>

This file is part of femto.

femto is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

femto is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with femto.  If not, see <http://www.gnu.org/licenses/>.
Source: https://github.com/specktator/scraper

*/
/* CONSTANTS */
define('ALPHA',TRUE);
define('APP_NAME',"femto");
define('ROOT_PATH',__DIR__."/");
define('ROOT_REL_PATH',"/".basename(__DIR__));
define('LIBRARYPATH',__DIR__.'/music/');
define('JSONDBPATH',__DIR__.'/jsondb/');
define('TEMPDIR',__DIR__.'/tmp/');

define('IMAGES_CACHE_DIR',__DIR__.'/images/.cache/');
define('IMAGES_CACHE_DIR_REL',ROOT_REL_PATH.'/images/.cache/');

define('IMAGES_SAVE_DIR',__DIR__.'/images/');
define('IMAGES_SAVE_DIR_REL',ROOT_REL_PATH.'/images/');

define('AUDIO_CACHE_DIR',__DIR__.'/music/.cache/');
define('AUDIO_CACHE_DIR_REL',ROOT_REL_PATH.'/music/.cache/');

define('AUDIO_SAVE_DIR',__DIR__.'/music/');
define('AUDIO_SAVE_DIR_REL',ROOT_REL_PATH.'/music/');

define('VIDEO_CACHE_DIR',__DIR__.'/video/.cache/');
define('VIDEO_CACHE_DIR_REL',ROOT_REL_PATH.'/video/.cache/');

define('VIDEO_SAVE_DIR',__DIR__.'/video/');
define('VIDEO_SAVE_DIR_REL',ROOT_REL_PATH.'/video/');

define('ID3_EXEC_TIME','120');
define('NORMAL_EXEC_TIME','30');

/* ini file */
// ini_set('memory_limit', '30MB');
// ini_set('display_errors',1);
// error_reporting(-1);
/*  autload files */

function autoload($className,$path='lib/'){

	// echo "$path <br>";
		foreach (glob(__DIR__.'/'.$path.'*',GLOB_ONLYDIR) as $key => $value) {
			// echo "path in: $value <br>";
			if(file_exists($value."/".strtolower($className).".php")){
				// echo "Found: $value/$className.php<br>";
				include_once($value."/".strtolower($className).".php");
				return TRUE;
			}else{
				
				if(autoload($className,$value."/") == TRUE) {break;};
			}
		}
		return FALSE;
}

// Next, register it with PHP.
spl_autoload_register('autoload');


?>