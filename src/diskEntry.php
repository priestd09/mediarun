<?php
class DiskEntry {
    private $dirpath;
    private $filename;

    public function __construct($dirpath, $filename) {
        $this->dirpath = $dirpath;
        $this->filename = $filename;
    }

    public function getDirpath() {
        return $dirpath;
    }

    public function getFilename() {
        return $filename;
    }

    public function getExtension() {
        Utils::getSubstringAfter ( $filename, '.' );
    }
}
?>
