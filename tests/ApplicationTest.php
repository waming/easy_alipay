<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Kernel\App;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;

class ApplicationTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    public function test_get_app_id()
    {
        $app = new App($this->getConfig());
        $this->assertEquals('9021000131660113', $app->getAppId());
    }

    public function testApplication()
    {
        $application = new Application($this->config);
        $this->assertEquals('9021000131660113', $application->getApp()->getAppId());

        $client = $application->getHttpClient();
        $data['grant_type'] = 'authorization_code';
        $data['code'] = '20230102111111';

        $response = $client->post('/v3/alipay/system/oauth/token', $data);
        $result  = $response->getData();    //json_decode后去除验签的数据
        var_dump($result);
        $this->assertNotNull($result, 'not empty');
        $this->assertSame(true, $response->isSuccess(), 'ok1');
        $this->assertSame(200, $response->getStatusCode(), 'ok2');
    }

    public function testApi()
    {
        $application = new Application($this->config);
        $client = $application->getHttpClient();

        $data = [
            'out_trade_no' => 'test.1231724922156',
        ];

        $response = $client->post('/v3/alipay/trade/query', $data);
        $result  = $response->getData();    //json_decode后去除验签的数据
        var_dump($result);
        $this->assertNotNull($result, 'not empty');
        $this->assertSame(true, $response->isSuccess(), 'ok1');
        $this->assertSame(200, $response->getStatusCode(), 'ok2');
    }
}
