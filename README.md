# easy_alipay
一个轻松完成支付宝开放平台的，非官方的sdk
3.0版本采用了最新的v3版本验签，后续的版本只支持最新的v3版本，其他版本请自行修改源码

# 环境要求
- PHP >= 8.0.2
- [Composer](https://getcomposer.org/) >= 2.0
- [ext-curl](https://www.php.net/manual/en/book.curl.php)
- [ext-mbstring](https://www.php.net/manual/en/book.mbstring.php)
- [ext-ctype](https://www.php.net/manual/en/book.ctype.php)

## 安装

```bash
composer require honghm/easy_alipay
```

## 基本使用

```php

use Honghm\EasyAlipay\Application;

$config = [
        'appId' => '9021000131661052',
        //支付宝公钥，非证书书模式，必填
        'alipayPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiyx50VZNnFJ+bGMnKFHRtaDEP50MrJRoStwgMeiXGNoS3pImuJIpGywThw6lGwnruqYW4B+WH5i9dFv0aKXKvcEZOCfKklTIHXK4Gbwf9RMq7+PfUocH9UPK1XWshqq+phyAGL3t21a8dtlltwGVai+4z3YhNXe9oE+T4S7a72HrBPpNx6Asf2RF+uWtRlpsH4ZOOwb2EdIDwIYsWI9dgKZBEPEUAJ6FHT/00t/8nqB/WuKZm8xOSEtGQTvIOgAusBCTgdpfqs2d4RJWx0txCG1+yQ0N5hJsIbHb+as8C7kyHOTwE3HIGsBjCq7GH9dE6Xj0FOLb5/dPcqTuxOUKOwIDAQAB',
        
        //应用私钥
        'privateKey' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCAX2duyqYOdkHhWD+WKOwjlArWFs4oX3in71vB1EZUdWtEKs9OZVBEPZvdRcGTEm/eLMyy1bNMDq/eoI0v9epaFSgU7PD9y82bdT898lT1MeedJjZtl+b4ECa5c/8O7TZ6C3/M0BeU2HOFUsJn0b2xW3XTJJYJVoECxTqt+FFJ1TrjAtpKtwYbQxPa3QwgZ/QfLb3IcrH9uS4EhN4hRURsLROp5m3AJ9FRKkqV9gEoMbCb1DVNU6+KbaKy0l8LoDrOj/WwmlZCOXoP+78GQFKkElBRUrrAV1eqbCukLbvlUgdge6UV7Oc52b+XhCZSg2o9Cfnf9Dr9xM3a3gWWUqQBAgMBAAECggEABUtqCbDD/o60EuY/4SKY8rK1a7UPFa0yXcLKhwkJSYx6OxqE6FfXYqkj15F79WPqR0CTxMB5n6ZvbIA0soiy/LooqAxJuD4CoenJDPYEuQQw6FNeJVEBjrOj1TxKhcQU3h3BTKI8hsASq6oJKJ0G9Nyv1GCK66Gsun+F9wzp1WCtYWynOfjEFgu6C1/NsDr0TCK4TxP6aGnei8Ks/YX+Tz/MxOen+MNa0eSUSTiRbVoS4VoUuTSyM0InL82ugEIy6nT+XTDdtU5wlW/DJt/8h+HTQ/4e1IW6sZZiGdM9OaOh8gvGzZTRx+QufLcrSbrPHspSGcUHboK7aCKfsza9bQKBgQD1u47Vu/0ey2kTf/b6CpEE6B1/eX6R9tng0Ge3rwMCmVlqzCfjfhOI1AQbf1nrtraulQPgwUg3sfWZ/Ck2lV8+UKtoJvh4gQ8Ke35Nzd24i3AeFeEHFgrHGvrwXZ7vZYzoM8D4h4oEHMzIt4y/QMYtQYfqzr3WbWTivGWeZL/AmwKBgQCFvIXbNHf670P5DYkPVeWovh4fpEJ/pXwMvcz0a7mzpDglDvP3zCKHcoD+VPFC0Rb4/Sg2r7XoLOqXKS1YAwVNgETjtLQPXbTpY2XNyS7aIeGjPOxF1gB+D9x46W0T07I4HSDhRomWzhEAQsfz6Xp7bxtPLVQ1EDeUeOpCdjJRkwKBgQDTg7Tnvp+a0SXJ9hzFjzDSk2VIo3BR/bP+8gREtG0X4J158u7mv0/bN/utG4pv/V7zmSq0XEpoPlMUT1u+Mwa/YbqYtOqs9xHWSQQnvmr5/XPtQZHngo6WYV/cZAl5MOT/vgR9KFWhQpT2tB9/RCcRL1XpxDjdiF4NWHLzyu7OvwKBgDEbjcd1V81ECKCuULLZ5s/0p9kUlvqKSKfhFh52ZCVL2vX4rKtEQTWdXTKG/GR9pMLfKuIR2Wkc/TNugmD8EvlnbWRz2V5/GUxOHHbZNiQRJuqrF1N5horDxkB77nrGVH34bDdskF9Y4bNH0maFSF2E6fFEAtTNVKtnTxYCku2TAoGATQSJDaR6egP2+kdfLCLYxRo18vHzaLTUvSPj5ZyfJCjmLA2OCtrmCS0+hYL21pP4o/ZzdcKQK8qCwkXVZRn8anYGmiYESRYyfJHrszcl6aBijVjR37AdQ4QtfQ0Cds4i1QKfHQ9aLVlXACYk8plprHH0SG3VuygkCjHDcjaGkHU=',
            
        //如果是证书模式，则填写以下参数
        'appPublicCertPath' => BAST_PATH . '/cert/appPublicCert.crt',
        'alipayPublicCertPath' =>  BAST_PATH . '/cert/alipayPublicCert.crt',
        'alipayRootCertPath' => BAST_PATH . '/cert/alipayRootCert.crt',
        //end
        'appSecret'  => 'f7naAgykq70ng3Lzv5oF1A==', //是否使用明文加密，如果不是，留空
        'http' => [
            'base_uri' => 'https://openapi-sandbox.dl.alipaydev.com/gateway.do',
            'timeout' => 5.0,
            'headers' => [],
        ],
    ];
    
    $application = new Application($config);
    $client = $application->getHttpClient();
    $data['grant_type'] = 'authorization_code';
    $data['code'] = '20230102111111';
    $response = $client->post('/v3/alipay/system/oauth/token', $data);
    $result   = $response->getData();    //json_decode后去除验签的数据
    $this->assertNotNull($result, 'not empty');
    $this->assertSame(true, $response->isSuccess(), 'ok1');
    $this->assertSame(200, $response->getStatusCode(), 'ok2');
    
    //h5、网页支付请求
    $payClient = new Pay($application);
    $object['notify_url']  = 'https://channel.baidu.cn/v2/pay/notify';
    $object['return_url'] = 'https://www.baidu.com';
    $object['out_trade_no'] = 'test.123'.time(); //test.1231724922156
    $object['total_amount'] = 99.0;
    $object['subject']      = 'test.123'.time();
    $object['product_code'] = 'FAST_INSTANT_TRADE_PAY';
    $response = $payClient->pageExecute('alipay.trade.page.pay', ['biz_content' => $object], 'GET');
    var_dump($response);
```

## 更多使用

```php
    
    //使用自定义request, request需实现Psr\Http\Message\ServerRequestInterface接口
    $appticaion->setRequest($request);
    
    //使用自定义httpClient，自己需要实现签名、验签
    class client implements Honghm\EasyAlipay\Kernel\Contract\HttpClientInterface
    {}
    $httpClient = new Client();
    $appticaion->setClient($httpClient);
```


## 更多使用可查看测试用例

## License

MIT
