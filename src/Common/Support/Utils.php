<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common\Support;

use Honghm\EasyAlipay\Common\Exception\InvalidConfigException;

/**
 * 工具类
 */
class Utils
{
    /**
     * 从证书中提取序列号
     * @param string $certPath
     * @return string
     * @throws InvalidConfigException
     */
    public static function getAppCertSN(string $certPath) : string
    {
        $cert = file_get_contents($certPath);
        $ssl = openssl_x509_parse($cert);

        if (false === $ssl) {
            throw new InvalidConfigException('Parse `app_public_cert_path` Error', 400);
        }

        return self::getCertSn($ssl['issuer'] ?? [], $ssl['serialNumber'] ?? '');
    }

    /**
     * 提取根证书序列号
     * @param string $certPath
     * @return string|null
     * @throws InvalidConfigException
     */
    public static function getRootCertSN(string $certPath): ?string
    {
        $sn = '';
        $exploded = explode('-----END CERTIFICATE-----', file_get_contents($certPath));

        foreach ($exploded as $cert) {
            if (empty(trim($cert))) {
                continue;
            }

            $ssl = openssl_x509_parse($cert.'-----END CERTIFICATE-----');

            if (false === $ssl) {
                throw new InvalidConfigException('Invalid alipay_root_cert', 500);
            }

            $detail = self::formatCert($ssl);

            if ('sha1WithRSAEncryption' == $detail['signatureTypeLN'] || 'sha256WithRSAEncryption' == $detail['signatureTypeLN']) {
                $sn .= self::getCertSn($detail['issuer'], $detail['serialNumber']).'_';
            }
        }

        return substr($sn, 0, -1);
    }

    protected static function getCertSn(array $issuer, string $serialNumber): string
    {
        return md5(
            array2string(array_reverse($issuer)).$serialNumber
        );
    }

    protected static function formatCert(array $ssl): array
    {
        if (str_starts_with($ssl['serialNumber'] ?? '', '0x')) {
            $ssl['serialNumber'] = hex2dec($ssl['serialNumberHex'] ?? '');
        }
        return $ssl;
    }
}