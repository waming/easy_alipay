<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common\Support;

function array2string(array $array) : string
{
    $string = [];
    if ($array) {
        foreach ($array as $key => $value) {
            $string[] = $key . '=' . $value;
        }
    }
    return implode(',', $string);
}

/**
 * 0x转高精度数字
 * @param $hex
 * @return int|string
 */
function hex2dec($hex): int|string
{
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
        $dec = bcadd((string)$dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
    }
    return $dec;
}