<?php
namespace xbeastmode\emailverify\utils;
class TokenUtils{
    /**
     * @param bool|false $entropy
     * @return string
     */
    public static function generateToken($entropy = false){
        $s = uniqid("", $entropy);
        $num = hexdec(str_replace(".", "", (string)$s));
        $index = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($index);
        $out = '';
        for ($t = floor(log10($num) / log10($base)); $t >= 0; $t--) {
            $a = floor($num / pow($base, $t));
            $out = $out . substr($index, $a, 1);
            $num = $num - ($a * pow($base, $t));
        }
        return $out;
    }
}