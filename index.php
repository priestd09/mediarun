<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

require_once ('modules/getID3-1.9.7/getid3.php');

$audioExtensions = array (
        'mp3',
        'm4a',
        'oga',
        'wav',
        'webma',
        'fla' 
);
$imageExtensions = array (
        'jpg',
        'jpeg',
        'gif',
        'png',
        'svg' 
);
$videoExtensions = array (
        'm4v',
        'ogv',
        'webmv',
        'flv' 
);
$textExtensions = array (
        'txt',
        'srt' 
);

$currentMediaPath = $_SESSION ['mediaPath'];
//echo "<pre>currentMediaPath from cookie: $currentMediaPath</pre>";
if (! isset ( $currentMediaPath ) || ! is_dir ( $currentMediaPath ))
	$currentMediaPath = 'media-root';

//echo "<pre>currentMediaPath: $currentMediaPath</pre>";

$nextDir = $_GET ['dir'];
if (isset ( $nextDir )) {
	//echo "<pre>nextdir: $nextDir</pre>";
	if ($nextDir == '..') {
		if ($currentMediaPath != 'media-root') {
			$lastIndex = lastIndexOf($currentMediaPath, '/');
			//echo "currentMediaPath: $currentMediaPath<br/>";
			//echo "lastIndex: $lastIndex";
			$currentMediaPath = substr($currentMediaPath, 0, $lastIndex);
			//echo "nextPath: ";
			//var_dump($nextPath);
		}
	} else {
		$nextMediaPath = "$currentMediaPath/$nextDir";
		if (is_dir ( $nextMediaPath ))
			$currentMediaPath = $nextMediaPath;
	}
}
//echo "<pre>currentMediaPath after nextDir: $currentMediaPath</pre>";
$_SESSION['mediaPath'] = $currentMediaPath;

$subdirs = array ();
$audiofiles = array ();
$otherfiles = array ();

$filenames = scandir ( $currentMediaPath );
foreach ( $filenames as $filename ) {
    if ($filename === '.')
        continue;
    
    if ($currentMediaPath == 'media-root' && $filename === '..')
        continue;
    
    $filepath = $currentMediaPath . '/' . $filename;
    if (is_dir ( $filepath )) {
        // echo "dir-$filepath";
        $subdirs [] = $filename;
    } else {
        // $isAudio = false;
        foreach ( $audioExtensions as $extension ) {
            if (endsWith ( $filename, ".$extension" )) {
                // echo "mp3-$filepath";
                $audiofiles [] = $filename;
                // $isAudio = true;
                break;
            }
        }
        if (end ( $audiofiles ) != $filename) {
            // echo "other-$filepath";
            $otherfiles [] = $filename;
        }
    }
}
//var_dump($audiofiles);

function startsWith($haystack, $needle) {
    return $needle === "" || strpos ( $haystack, $needle ) === 0;
}

function endsWith($haystack, $needle) {
    return $needle === "" || substr ( $haystack, - strlen ( $needle ) ) === $needle;
}

function lastIndexOf($string, $item) {
    $index = strpos ( strrev ( $string ), strrev ( $item ) );
    if ($index) {
        $index = strlen ( $string ) - strlen ( $item ) - $index;
        return $index;
    } else {
        return -1;
    }
}

function getIconPath($filename) {
    global $audioExtensions;
    global $imageExtensions;
    global $videoExtensions;
    global $textExtensions;
    
    $fileExtension = getSubstringAfter ( $filename, '.' );
    $extensionFound = false;
    foreach ( $audioExtensions as $extension ) {
        if (strtolower ( $fileExtension ) == $extension) {
            $extensionFound = true;
            return '../images/extensions/icon_music.png';
        }
    }
    foreach ( $imageExtensions as $extension ) {
        if (strtolower ( $fileExtension ) == $extension) {
            $extensionFound = true;
            return '../images/extensions/icon_image.png';
        }
    }
    foreach ( $videoExtensions as $extension ) {
        if (strtolower ( $fileExtension ) == $extension) {
            $extensionFound = true;
            return '../images/extensions/icon_video.png';
        }
    }
    foreach ( $textExtensions as $extension ) {
        if (strtolower ( $fileExtension ) == $extension) {
            $extensionFound = true;
            return '../images/extensions/icon_text.png';
        }
    }
    return '../images/extensions/icon_sans.png';
}

function getSubstringAfter($string, $after) {
    $lastIndex = lastIndexOf ( $string, $after );
    return substr ( $string, $lastIndex + 1 );
}

function getFileGroup($categoryName, $filenames, $isDir) {
    if (count ( $filenames ) == 0)
        return "";
    
    $html = '<div class="filegroup">';
    $html .= '  <span class="category">' . $categoryName . ':</span>';
    $html .= '  <div class="list">';
    foreach ( $filenames as $filename ) {
        if ($isDir) {
            $iconStyle = 'background-image: url(\'../images/extensions/icon_folder.png\');';
        } else
            $iconStyle = 'style="background-image:url(\'' . getIconPath ( $filename ) . '\');"';
        $html .= '<div class="file-square" onclick="window.location = \'?dir=' . $filename . '\'" title="' . $filename . '">';
        $html .= '  <div class="square-icon" ' . $iconStyle . '></div>';
        $html .= '  <div class="square-text">' . $filename . '</div>';
        $html .= '</div>';
    }
    $html .= '  </div>';
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="plugin/css/style.css">
<link rel="stylesheet" type="text/css" href="css/demo.css?reload">
<script src="js/jquery-1.6.1.min.js"></script>
<script src="plugin/jquery-jplayer/jquery.jplayer.js"></script>
<script src="plugin/ttw-music-player.js"></script>

<?php
if (count ( $audiofiles ) > 0) {
$getID3 = new getID3 ();
?>
<script>
var myPlaylist = [
<?php
foreach ( $audiofiles as $audiofile ) {
    $audiofilepath = $currentMediaPath . '/' . $audiofile;
    $ThisFileInfo = $getID3->analyze ( $audiofilepath );
    $fixedFileName = str_replace('\'', '\\\'', $audiofilepath);
    $fixedTitle = str_replace('\'', '\\\'', $ThisFileInfo['tags']['id3v2']['title'][0]);
    if (strlen($fixedTitle) == 0)
            $fixedTitle = getSubstringAfter ( $fixedFileName, '/' );
    ?>
    {
        mp3:'<?=$fixedFileName?>',
        // oga:'mix/1.ogg',
        title:'<?=$fixedTitle?>',
        artist:'<?=$ThisFileInfo['tags']['id3v2']['artist'][0]?>',
        album:'<?=$ThisFileInfo['tags']['id3v2']['album'][0]?>',
        rating:0,
        duration:'<?=$ThisFileInfo['playtime_string']?>',
        cover:'<?="$currentMediaPath/folder.jpg"?>'
    },
	<?php
}
?>
];
</script>
<script>
var description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce id tortor nisi. Aenean sodales diam ac lacus elementum scelerisque. Suspendisse a dui vitae lacus faucibus venenatis vel id nisl. Proin orci ante, ultricies nec interdum at, iaculis venenatis nulla. ';
$(document).ready(function() {
    $('#audioplayer').ttwMusicPlayer(myPlaylist, {
        autoPlay: true,
        description: description,
        tracksToShow: 20,
        jPlayer: {
            swfPath:'plugin/jquery-jplayer'
        }
    });
});
</script>
<?php
}

?>
</head>
<body>
    <div>
        <div id='audioplayer'></div>
        <div id='filelist'>
            <?=getFileGroup('Folders', $subdirs, true)?>
            <?=getFileGroup('Other', $otherfiles)?>
        </div>
    </div>
</body>
</html>