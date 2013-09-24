<?php
class Utils {

    /**
     *
     * @param String $string
     *            string to shearch in
     * @param String $after
     *            the string after that we whant the substring
     */
    public static function getSubstringAfter($string, $after) {
        $lastIndex = lastIndexOf ( $string, $after );
        return substr ( $string, $lastIndex + 1 );
    }
}
?>