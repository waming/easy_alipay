<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel\Support;

use JetBrains\PhpStorm\Pure;

function array2string(array $array) : string
{
    $string = [];
    foreach ($array as $key => $value) {
        $string[] = $key.'='.$value;
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
    $dec = '0';
    $len = strlen($hex);

    for ($i = 1; $i <= $len; ++$i) {
        $dec = bcadd(
            $dec,
            bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i), 0), 0),
            0
        );
    }

    return $dec;
}

#[Pure(true)]
function getPublicCert(string $key): string
{
    return Str::endsWith($key, ['.cer', '.crt', '.pem']) ? file_get_contents($key) : $key;
}