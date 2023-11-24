<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests;

use Honghm\EasyAlipay\Common\Config;
use Honghm\EasyAlipay\Common\Contract\ConfigInterface;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected array $config = [
        'appId' => '9021000131661052',
        'privateKey' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDQkH/x4lzBx+j7OEGPibSb+Y3jARlWu99/rgIp4og1wmNCkISzvPmjuMz/DIyOSHmCiqr55kChbJS7Ht1GHAulUSuSZ6UxLcCONM54RAd2Tl7vpcSSPbci+t4+KZkWgdT9CTMAXz/K4rSODC1jQk13OWKVbjOb9Jptu6LvFVCbvEjgK2/BnCKndaKxMy5e1iwKct5N4Y5dNMl/rdkCel96rfXRO7OW81yqqAPfh0Zqb+UqqwoEn+7Mp/8+hF9tN/mGBLJEl556E+JtDNmpOzZwNotJjDOhgXPYAGdNk4Z27PKif/8JYIEU20C/6uItnlTlOcHz5u85oVxkupLW8bNFAgMBAAECggEAOAxdFdfLLZhXmAuKMtTC4Jez9ak58gETEvDkS/VAvZG7swu7XcIPJFxpWrE4pJP9c4NoZzflbnwLjC3DMTAgQRELMJEB57Fv6cdvJQrDJpvxD13w8Yv8Rz47s2DibxX88vMh7ZurPQ6njvTezannv36Aa7/txp95ULv4EWAdBXybrqjOChmaZ7/+e2P3KH/nCLJanpOYSrzsXVX5WlB0Xp0kZSlyLOf+By9fetZaPoASZsD6waPccp5vnzNW7LoiJEHBBqOgdjaOBjVSPXeNt1TN3lvIv9mOZCblGcIIsD3CyicK8BfpTVqxm3ZinNaNhZbHw67XG1i/cs8zNtXNkQKBgQDsktV7Tfwl7kO+0McXSUOURVFFQlBp8qgBG/v6l1/Cloa96LYcToaweqrGqpaldgFL4gPWOhQYy7uWZ4DzSIDClGG0uiuxr/5/GLwZ0ex3cQKxzGi5hq14X9SCHpyMVvA+GdF4Tj/yyTPZIBawjOu8g3DqBUzTEdrtJJVjcf6pHwKBgQDhsN464LRyJqRIEIdDf4V1ocK4QQaMOWbx/IQ/C+YhLZ94h2XL+9HbAP1u0/2FREkkYIAb20+vxKVxiZVFL+v3TTZN1E+QjJVbAQHYC7nMNhw59XgHVmPo5bFQNrXSfz8eHxGkkgmRxAIfJdjBxgIKkjZr+/62ql1UvoICPxWDGwKBgBnpMyygK//TiuP5ZfTs91PkC7S0QK55/2CvufeGYNylM/jU5i1PhH0L0myT/o/8zsOK67SpA857cf5VM9BxnnixW8o5odOCXTN6eA+z6FxkqlDi/I1lbNaEWHgv9iGA3CtRFJpCp8plsyIS26lWpfMtyk43amSrzfivSxVrROMtAoGASnI/PAi3PRhGBId/NuKvsVfElWbNtB+TN6tmLC3OoY8dFXMEPz83wBgZgR9odzdJMTiryYSUSpSBRmxt0r62BYNwsEeiXzogYj54zz0+8n++29d+2lzC5CYwURda7q6OsW7qMPEDOxP+5ytizrh0H+yPCxu6r6KO2s9krz0D6ZUCgYEAtqdBX0T/DJGyArpzrI1VeQkjYkHq8AJlvKzhk9C++OyJNnOWLBZxNVjEjLFgagmqekGW2V85uMwg2empy2g0ib2fy1fg2iI5pFMJ34QmWZ4kjdm4p8Qm8MFgEqnUC8n6wHkzCNDafMckUzlAC08eVD7B7uuokTLwYZVHaqIBT64=',
        'appPublicCertPath' => BAST_PATH . '/cert/appPublicCert.crt',
        'alipayPublicCertPath' =>  BAST_PATH . '/cert/alipayPublicCert.crt',
        'alipayRootCertPath' => BAST_PATH . '/cert/alipayRootCert.crt',
        'notify_url' => 'https://www.baidu.com/',
        'http' => [
            'base_uri' => 'https://openapi-sandbox.dl.alipaydev.com/gateway.do',
            'timeout' => 5.0,
            'headers' => [],
        ],
    ];

    protected array $miniConfig = [
        'appId' => '9021000131659150',
        'privateKey' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCNQ8BVQE4Cyebrgw5RAV9IV5FSCfasZSrUc270pJQk390i3DANs+N/EB01R2XAB5MZHDDuPyogZsivzflU2tYez2HpgCeCmmUS7eDg+X4Sdu8d+0fLMGPGYDYm3vDiQdCGcWZpreQNgoAVCMN9W81H90Obo353rLurn30hP6ClebrCD5KaTkIXvprSaNuAEBvCEWxSJRsimDIeP5/kcU1Lovs7HrxrUWIn5hgiZ3SPOy2uDotosd1CmrP5Mr1VDkUdrPAgMBzlzOgnnUrirgGsDHpcjYkuAE/iJH1D9kv6t41q8lkY/Endpf7MpVzkXsh+b2c7Nq9taF4BM8jklE67AgMBAAECggEAV63WodNQ2DBC8KieaS7l2fvoCbh/b483XzfqhVPlU2HTWoC6Hrcpm8L/rgMLp33MEkjKIgX4erXxGBVLDRzqy6LCTfCqFUkJNQ+KhXi1hHZngf9mFzqIoycyVTwI3RYmfFvEShGRO8VlBCk1VQh2yJUzVFXM+DCxJxvNyaY5aprIpHHw8KNLfqWgafCwl2WJ/xW0hu6ztGnuXnFNqr7nlNlvxJ90WvujeKWVJuuokB6JwHOtBWsnjZlNzYlIxGjNqZmXRTRDcO4YeE6f85A4hHOhQDuyoUnPDiHy1vu6DuiWw66zPdBXeN8+UemaVGP+1DLlmIi28GtWIus3nsJDeQKBgQD156bNbk2z2RV356lMP1nhtVCbGwBZO32DuECIpGosTziUDbKpn2zOHIfBz7y/W+OTtSToT3yA5jKnnXbl343HX8FXTunNtykH1tRgLd449scGPG3uayOm1QgFjgvMWKlLyhAiLoZBjOBDBaPlD8hGvDhsBaiSZIo4CQefpAFblQKBgQCTEGDlBp4ONeSidQctEutsT9su3voLvLaveoJL8fQe12qIBDYGbuzzuo21onG3n/xnRacWOxkFeyHStGooF9BEVIctz4jG6htmSEtY/fqFxc2otvWtn3IdpqQETrSjgHtwEPyNn83ZlcajeJByvTFoL9/Hs1wiRrmEScpbZhvtDwKBgF5qWk4oGueB409NgGBJNq6F2nQjUufwAoovlX+heS6YIEYgWEfucW1V4P4WUAc9Nc0B0TDtTAWF3U8kE4HBawNxDaADKZwVxkg/QP3Ivrkqb1JCo4bWVjL5OoI/fuIv7Jiv9a/aIyxJ9dVl9f6+J4yZOiSnq4jB15waQ3YVF3xdAoGBAIfovQMrSGptl+wjJwyazYL9kdwRKRgrwNEO8NdqtWbDQaqN6besT8M3BrtzcpB2g/aUwkOjPg54qttk7C49Q7XCQGMvxoG93LB741ZM8XcrRFLFMurPzdBlLnLkob/wfyMkL6JaqwyIhFiSlTvUaJKWm8KcmWr73XrKDv7EsM41AoGAO0l39+IyPiKpnU3Tw6Eb+ehFDID252XoS2H30XNlFRDkslf16W5Du3dQi3he/GBUglnrvzkgs+Skzw7OyuBjd946Y3vUP3rCg6wFqgg59gVUNKIXA92wBRpf5tv2/X4IfS8oCegT1JZdmMjmTCj2Lzg816ochyMA80I4QTLsQp0=',
        'appPublicCertPath' => BAST_PATH . '/cert/appMiniPublicCert.crt',
        'alipayPublicCertPath' =>  BAST_PATH . '/cert/alipayMiniPublicCert.crt',
        'alipayRootCertPath' => BAST_PATH . '/cert/alipayMiniRootCert.crt',
        'notify_url' => 'https://www.baidu.com/',
        'http' => [
            'base_uri' => 'https://openapi-sandbox.dl.alipaydev.com/gateway.do',
            'timeout' => 5.0,
            'headers' => [],
        ],
    ];

    #[Pure]
    protected function getConfig(): ConfigInterface
    {
        return new Config($this->config);
    }

    #[Pure]
    protected function getMiniConfig() : ConfigInterface
    {
        return new Config($this->config);
    }
}