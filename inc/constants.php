<?php
class Constants {
    private static $audioExtensions = array (
            'mp3',
            'm4a',
            'oga',
            'wav',
            'webma',
            'fla'
    );
    private static $videoExtensions = array (
            'm4v',
            'ogv',
            'webmv',
            'flv'
    );
    private static $imageExtensions = array (
            'jpg',
            'jpeg',
            'gif',
            'png',
            'svg'
    );
    private static $textExtensions = array (
            'txt',
            'srt'
    );

    public static function getAudioExtenstions() {
        return self::$audioExtensions;
    }

    public static function getVideoExtenstions() {
        return self::$videoExtensions;
    }

    public static function getImageExtenstions() {
        return self::$imageExtensions;
    }

    public static function getTextExtenstions() {
        return self::$textExtensions;
    }
}
?>