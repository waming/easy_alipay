<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests;

use Honghm\EasyAlipay\Kernel\Config;
use Honghm\EasyAlipay\Kernel\Contract\ConfigInterface;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected array $config = [
        'appId' => '9021000131660113',
        'alipayPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiyx50VZNnFJ+bGMnKFHRtaDEP50MrJRoStwgMeiXGNoS3pImuJIpGywThw6lGwnruqYW4B+WH5i9dFv0aKXKvcEZOCfKklTIHXK4Gbwf9RMq7+PfUocH9UPK1XWshqq+phyAGL3t21a8dtlltwGVai+4z3YhNXe9oE+T4S7a72HrBPpNx6Asf2RF+uWtRlpsH4ZOOwb2EdIDwIYsWI9dgKZBEPEUAJ6FHT/00t/8nqB/WuKZm8xOSEtGQTvIOgAusBCTgdpfqs2d4RJWx0txCG1+yQ0N5hJsIbHb+as8C7kyHOTwE3HIGsBjCq7GH9dE6Xj0FOLb5/dPcqTuxOUKOwIDAQAB',
        'privateKey' => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC1Hdvrj5tjDKW7onIaH8D0LvUC08eOY8MWJiR8yCIXygxrSf5MpkL7Awcm/D3RBajU9nz3iD12XXX9XVobL0O2RCqp8rc948U1KrGrOHNIH46XoFIJzfCAFnF9SKjQ1HPDTK2aU2AhaMTDh6DTMWegmU7FVlCrbsKGjXiwYif/KMyBZLOyWQYsfRxdPbAvMpk9OQUGqp36xxoTIQzgWjxEwmlxGV0/An8oR1NJbzHUXwKwXz0srAkYWwiLN442qqx5S8SYA/EbxcGPE7mCT9fGjEXGUidYgis+3ZY/NgkEaAsK8g5pNAwgENHsDl7SnXIxCEzNjyhJCnz1frkk392RAgMBAAECggEAccKLe+37n7816iEioh4HyFgmNNxon5sx0hurF2VtZJvYKRuUyvgzYg6I04fkHAH+BaW84I2GbW5OyhF0o1aQpU8zrQNy7UC/gaGLbwxJ/Kc1nS9p2BauVQXDyvl0b5bCD41+DU0JOHLiBB6QeZayjEE/JNwOV19+d50sjT1Z+f3I8puJz0yN50X+Hp/v8eZipEwN3Vt+WiXEt/r7ozoR8IuZsHE9mJt5AzWFSB06OsTfp9bDHoCIAd7mss6ZIDMWZNoCjxphCkL49iE/7oKL73IEzpe9l1poVEVWB3hGkR8vgl1BPSC1Y+DlxK9gGmTtTLG6Rag6EVgBC1Ha+23HcQKBgQD5LhObwHc00zwJTQMfod/VyFHRTeIBIdvZgF8lnyjUacPIJbq/bFwOxP02o07lFyBtiTm87uxhJaUGoElfxvShnUseAajIdQeqrcUZ78jLVJ4Tr1JBTKzYnTdWS8ky2fh7HVmLH15xlY2Byo6Am/G08NVrV4Qt4nQxg7R2NJrfYwKBgQC6EuJ3hq0faTNPL2K/mzCONIHLKKSeZ4IO6OFfvaM05JZV5Qk0QxVgWdh/61c8i39xxlBLWMQq+wBiLXdGJplRu1Zzu9BMqv8LtDFn7Q+nKXqlA35K/8S1H8kpW72+8ppYXsCdl686o9jzgdzGfuE5i+17mOdVFtimOLssGCQjewKBgQDoYdc5lunVIvZHekHiezVji1j6SJbwFWKR2g1h6afLSQOkIcOih+zmfUHkdbK7JbFBVgu5rlxWUSQxJMSpBMPaAPYv+r4sjC/lNf+uK5hoP/bYRtYAPgCoVx5nZXAy82KvdnSusUeDyERa+cuiF0z4QO3YGJsVq65PCxP+dRrGHwKBgHCQSAwCnuxnmbCHe/dpjGAah4I9HgjtmRIn818Vu4ud1Qw1N/IzutTExWHtHqLzyK8tRxmto05U9ZBu7L61Tv7Qk6YDsikPMKcF5PV3xYZcY2M7z8TTIU5o4ipw40KGRLS87UzonAqfX/k/UC2MR/emHI4um5Mv9PPpUeGE4SfNAoGBAPIWnKn/Wjz/11y1rS41CSOuOd0ZhVqcrr1zTJEv5R6aeelxA/ERcBD61b3Jta4SlFiYIz2GsVDNzu7Lw6v6gH+His6juo24pdlA9aKh2k2zhf2Da+5VIZjGNLy4x0sDxvVAD5xZqz40/PKcL6mfBck0CM6kw3sgc9MhpWfPTUyC',
        'appPublicCertPath' => BAST_PATH . '/cert/appPublicCert.crt',
        'alipayPublicCertPath' =>  BAST_PATH . '/cert/alipayPublicCert.crt',
        'alipayRootCertPath' => BAST_PATH . '/cert/alipayRootCert.crt',
        'appSecret'  => 'f7naAgykq70ng3Lzv5oF1A==',
        'notify_url' => '',
        'http' => [
            'base_uri' => 'https://openapi-sandbox.dl.alipaydev.com', //https://openapi.alipay.com
            'timeout' => 5.0,
            'headers' => [],
        ],
    ];

    protected array $miniConfig = [
        'appId' => '9021000131661049',
        'privateKey' => 'MIIEpQIBAAKCAQEAoF4I240U7R6VYhILZGsNubLRi7NTFTlTQtIwJuWAY318/W/pvSmJb1F1GvV0qSbhtWSzhSX2mVlB2V3pDH/YJTQj2eFzCPEB8TTVDQiOJ3dSKFjkeYcdnM2ycD5cszkSOSEvMJIZa3V/fGfMdIOSQsKCUlS3Cbu0GVpZ9+nVP2UpAnHScWqo5cK0a/rlFjfvEw/CXWsV8wInpDVA+oms1yAORio20FKTjvFivOFxQwGNXX6KmuQcbhHlbAW3NNjOz78Vsb0ZK7OcohJZJUBmy4No7FiSa+KmL1IJg/NaU2YOR/hOU4YJdmdqWgQldmwFuWLDd842vr/C30gU3sAybwIDAQABAoIBABCCfdD0jlH3ExP8nZWh+9DBWxdRx9zogOzhLLnkaLpVYffJfLvPuelu7mpvGKqNaTE+g8jSZrfU25WgOGzkAIGcA5Dbx/cEsNl/QevMlK31d8zVkff89Ax/lL4/fVWqK3kcNHqAoANTHmPVX9Qi83zR/46wUAO9gkYV6is+tIiJG8404E46qlCJtr1cYB0fuALKeSDe2lxaI6OJKEhMAsmzRjFliFxrrm50D/nJL1G9nHepxHXG3+efmYzdts5QdL1kJU2hWGQS146F0laH3+0ei1pJ2JGzGSCO04H5kBvatSehA77l3n5WDrjXPGuya4W3gZG0yFE1MEca+LzWuyECgYEA/dTVRpZO7FjIYSxwW6p4R19sWfmpyFcq0ZoyGCPgrx3w7QwhYqzL9ZB4MlshbkB71z/7EBnPStqVcLW3r20EXXZsrfv5RMcL9ORMK5329cQSkh2Ky0QDxEJRtEyG2BtOvfAabR6xufVnSOpk404SkX+/ojPZSl2or0CrOq/INr8CgYEAobzIH4Z1hscuVY9UmiJ8y3ZhWjlV9EidGCD5t/q7irrQM3OkXYgI4WAU2hsOeTRYBTcxI1Q4YhTQYPbV4MY58Aux/p3zQeFTeWY5y3iOmAjTqYKu3CfE+WMx4jguFvBi24rksfiNiEvly82R7jsPr1W2vEqoxXNIktSiPwExIFECgYEAyBSbJO/YOE7nbEmGK4tdEg1ysB2vKj8jfmLIN+UX95FnVsMoEcrZXrDJPhA9ctWbiJljQlbF4mAYSebDaqFJPo7Zre8floo7r4bBT+RNRX6PkON+gykSuwH3u2JTLEeiMu/vNLJsL4P0LTKt4cqpskz7dlTeY9Md4z7begATlqsCgYEAh+a00aiNwH5FNKeHEyy/Z8dE7icXTnhlaAztR+ZrB5sJrvIQvna734R4gPwTIU0WWOuTYng2GFQc9Bz0gjaOIGHzYlseN9E57twy3AGk76MbF2gzvdG89UCgfdFvCN9ccdU3bUPIySndIW7OnDsMZuvC7Fxfi+nepWH+IqrBuMECgYEAkMq79vokBm02Jkh72lsrkTGuFBb7j/I9WVW4CpnOpDQP1ldPPqGwJdsG8QOVml0TIzG0UyIgdVlAzp5yzB3iJDwAKWfYvIpruZ21IQSV4+lk3I8xGxTUiSi8ckk0j+4pPxmb7sAP+G68zZFyF/UziH7t6LGqdrbsXDhDfx6dktc=',
        'appPublicCertPath' => BAST_PATH . '/cert/appMiniPublicCert.crt',
        'alipayPublicCertPath' =>  BAST_PATH . '/cert/alipayMiniPublicCert.crt',
        'alipayRootCertPath' => BAST_PATH . '/cert/alipayMiniRootCert.crt',
        'notify_url' => 'https://www.baidu.com/',
        'http' => [
            'base_uri' => 'https://openapi-sandbox.dl.alipaydev.com',
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
