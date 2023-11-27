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
        'appId' => '9021000131661052',
        'privateKey' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCAX2duyqYOdkHhWD+WKOwjlArWFs4oX3in71vB1EZUdWtEKs9OZVBEPZvdRcGTEm/eLMyy1bNMDq/eoI0v9epaFSgU7PD9y82bdT898lT1MeedJjZtl+b4ECa5c/8O7TZ6C3/M0BeU2HOFUsJn0b2xW3XTJJYJVoECxTqt+FFJ1TrjAtpKtwYbQxPa3QwgZ/QfLb3IcrH9uS4EhN4hRURsLROp5m3AJ9FRKkqV9gEoMbCb1DVNU6+KbaKy0l8LoDrOj/WwmlZCOXoP+78GQFKkElBRUrrAV1eqbCukLbvlUgdge6UV7Oc52b+XhCZSg2o9Cfnf9Dr9xM3a3gWWUqQBAgMBAAECggEABUtqCbDD/o60EuY/4SKY8rK1a7UPFa0yXcLKhwkJSYx6OxqE6FfXYqkj15F79WPqR0CTxMB5n6ZvbIA0soiy/LooqAxJuD4CoenJDPYEuQQw6FNeJVEBjrOj1TxKhcQU3h3BTKI8hsASq6oJKJ0G9Nyv1GCK66Gsun+F9wzp1WCtYWynOfjEFgu6C1/NsDr0TCK4TxP6aGnei8Ks/YX+Tz/MxOen+MNa0eSUSTiRbVoS4VoUuTSyM0InL82ugEIy6nT+XTDdtU5wlW/DJt/8h+HTQ/4e1IW6sZZiGdM9OaOh8gvGzZTRx+QufLcrSbrPHspSGcUHboK7aCKfsza9bQKBgQD1u47Vu/0ey2kTf/b6CpEE6B1/eX6R9tng0Ge3rwMCmVlqzCfjfhOI1AQbf1nrtraulQPgwUg3sfWZ/Ck2lV8+UKtoJvh4gQ8Ke35Nzd24i3AeFeEHFgrHGvrwXZ7vZYzoM8D4h4oEHMzIt4y/QMYtQYfqzr3WbWTivGWeZL/AmwKBgQCFvIXbNHf670P5DYkPVeWovh4fpEJ/pXwMvcz0a7mzpDglDvP3zCKHcoD+VPFC0Rb4/Sg2r7XoLOqXKS1YAwVNgETjtLQPXbTpY2XNyS7aIeGjPOxF1gB+D9x46W0T07I4HSDhRomWzhEAQsfz6Xp7bxtPLVQ1EDeUeOpCdjJRkwKBgQDTg7Tnvp+a0SXJ9hzFjzDSk2VIo3BR/bP+8gREtG0X4J158u7mv0/bN/utG4pv/V7zmSq0XEpoPlMUT1u+Mwa/YbqYtOqs9xHWSQQnvmr5/XPtQZHngo6WYV/cZAl5MOT/vgR9KFWhQpT2tB9/RCcRL1XpxDjdiF4NWHLzyu7OvwKBgDEbjcd1V81ECKCuULLZ5s/0p9kUlvqKSKfhFh52ZCVL2vX4rKtEQTWdXTKG/GR9pMLfKuIR2Wkc/TNugmD8EvlnbWRz2V5/GUxOHHbZNiQRJuqrF1N5horDxkB77nrGVH34bDdskF9Y4bNH0maFSF2E6fFEAtTNVKtnTxYCku2TAoGATQSJDaR6egP2+kdfLCLYxRo18vHzaLTUvSPj5ZyfJCjmLA2OCtrmCS0+hYL21pP4o/ZzdcKQK8qCwkXVZRn8anYGmiYESRYyfJHrszcl6aBijVjR37AdQ4QtfQ0Cds4i1QKfHQ9aLVlXACYk8plprHH0SG3VuygkCjHDcjaGkHU=',
        'appPublicCertPath' => BAST_PATH . '/cert/appPublicCert.crt',
        'alipayPublicCertPath' =>  BAST_PATH . '/cert/alipayPublicCert.crt',
        'alipayRootCertPath' => BAST_PATH . '/cert/alipayRootCert.crt',
        'notify_url' => '',
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