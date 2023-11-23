<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common\Support;

/**
 * 工具类
 */
class Utils
{
    /**
     * 从证书中提取序列号
     * @param string $certPath
     * @return string
     */
    public static function getCertSN(string $certPath) : string
    {
        $cert = file_get_contents($certPath);
        $ssl = openssl_x509_parse($cert);
        return md5(array2string(array_reverse($ssl['issuer'])) . $ssl['serialNumber']);
    }

    /**
     * 提取根证书序列号
     * @param string $certPath
     * @return string|null
     */
    public static function getRootCertSN(string $certPath): ?string
    {
        $cert = file_get_contents($certPath);
        $array = explode("-----END CERTIFICATE-----", $cert);
        $SN = null;
        for ($i = 0; $i < count($array) - 1; $i++) {
            $ssl[$i] = openssl_x509_parse($array[$i] . "-----END CERTIFICATE-----");
            if(str_starts_with($ssl[$i]['serialNumber'], '0x')){
                $ssl[$i]['serialNumber'] = hex2dec($ssl[$i]['serialNumberHex']);
            }
            if ($ssl[$i]['signatureTypeLN'] == "sha1WithRSAEncryption" || $ssl[$i]['signatureTypeLN'] == "sha256WithRSAEncryption") {
                if ($SN == null) {
                    $SN = md5(array2string(array_reverse($ssl[$i]['issuer'])) . $ssl[$i]['serialNumber']);
                } else {
                    $SN = $SN . "_" . md5(array2string(array_reverse($ssl[$i]['issuer'])) . $ssl[$i]['serialNumber']);
                }
            }
        }
        return $SN;
    }
}