# easy_alipay
一个轻松完成支付宝开放平台的，非官方的sdk


# 环境要求
- PHP >= 8.0.2
- [Composer](https://getcomposer.org/) >= 2.0

## 安装

```bash
composer require honghm/easy_alipay
```

## 基本使用

```php

use Honghm\EasyAlipay\Application;

$config = [
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
    
    $application = new Application($config);
    
    $client = $application->getHttpClient();

    $data['grant_type'] = 'authorization_code';
    $data['code']       = '4b203fe6c11548bcabd8da5bb087a83b';
    $response = $client->post('alipay.system.oauth.token', $data);

    $body     = $response->getBody()->getContents();
    $data     = json_decode($body, true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertNotNull($data['error_response'], 'ok');
    $this->assertEquals(40002, $data['error_response']['code']);

```

## 更多使用可查看测试用例

## License

MIT