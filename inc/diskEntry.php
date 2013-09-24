<?php
class DiskEntry {

    // class fields
    private $dirpath;
    private $filename;

    // cached values
    private $isDir;
    private $iconPartName;

    public function __construct($dirpath, $filename) {
        include_once 'inc/utils.php';
        include_once 'inc/constants.php';

        $this->dirpath = $dirpath;
        $this->filename = $filename;
    }

    public function getDirpath() {
        return $this->dirpath;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getPath() {
        return $this->dirpath . '/' . $this->filename;
    }

    public function isDir() {
        if ($this->isDir == null)
            $this->isDir = is_dir ( $this->getPath () );
        return $this->isDir;
    }

    private function getExtension() {
        return strtolower ( Utils::getSubstringAfter ( $this->filename, '.' ) );
    }

    public function isAudio() {
        return in_array ( $this->getExtension (), Constants::getAudioExtenstions () );
    }

    public function isVideo() {
        return in_array ( $this->getExtension (), Constants::getVideoExtenstions () );
    }

    public function isImage() {
        return in_array ( $this->getExtension (), Constants::getImageExtenstions () );
    }

    public function isText() {
        return in_array ( $this->getExtension (), Constants::getTextExtenstions () );
    }

    public function getIconPartName() {
        if ($this->iconPartName == null) {
            if ($this->isDir ())
                $this->iconPartName = 'folder';
            else {
                if ($this->isAudio ()) {
                    $this->iconPartName = 'music';
                } else if ($this->isVideo ()) {
                    $this->iconPartName = 'video';
                } else if ($this->isImage ()) {
                    $this->iconPartName = 'image';
                } else if ($this->isText ()) {
                    $this->iconPartName = 'text';
                } else
                    $this->iconPartName = 'sans';
            }
        }
        return $this->iconPartName;
    }
}
?>
