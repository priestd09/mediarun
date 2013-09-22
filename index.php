<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

session_start();

require_once ('modules/getID3-1.9.7/getid3.php');

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
		//echo "dir-$filepath";
		$subdirs [] = $filename;
	} else if (endsWith ( $filename, ".mp3" )) {
		//echo "mp3-$filepath";
		$audiofiles [] = $filename;
	} else {
		//echo "other-$filepath";
		$otherfiles [] = $filename;
	}
}
//var_dump($audiofiles);

function startsWith($haystack, $needle) {
	return $needle === "" || strpos ( $haystack, $needle ) === 0;
}

function endsWith($haystack, $needle) {
	return $needle === "" || substr ( $haystack, - strlen ( $needle ) ) === $needle;
}

function lastIndexOf($string, $item){
	$index=strpos(strrev($string),strrev($item));
	if ($index){
		$index = strlen($string)-strlen($item)-$index;
		return $index;
	} else
		return -1;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="plugin/css/style.css">
<link rel="stylesheet" type="text/css" href="css/demo.css">
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
    ?>
    {
        mp3:'<?=str_replace('\'', '\\\'', $audiofilepath)?>',
        // oga:'mix/1.ogg',
        title:'<?=str_replace('\'', '\\\'', $ThisFileInfo['tags']['id3v2']['title'][0])?>',
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
        <?php
        foreach ( $subdirs as $subdir ) {
            ?>
            <a href="?dir=<?=$subdir?>"><?=$subdir?></a>
            <br />
            <?php
        }
        ?>
        <?php
        foreach ( $otherfiles as $otherfile ) {
            ?>
            <a href="?dir=<?=$otherfile?>"><?=$otherfile?></a>
            <br />
            <?php
        }
        ?>
    </div>
    <div class="file-square" onclick="alert('click1')">
        <div class="square-icon"></div>
        <div class="square-text"><?=$otherfile?></div>
    </div>
    
</div>
</body>
</html>