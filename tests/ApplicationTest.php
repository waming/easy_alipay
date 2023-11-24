<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Common\App;
use Honghm\EasyAlipay\Common\Exception\InvalidConfigException;
use JetBrains\PhpStorm\Pure;
use stdClass;

class ApplicationTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    public function test_get_app_id()
    {
        $app = new App($this->getConfig());
        $this->assertEquals('9021000131661052', $app->getAppId());
    }

    #[Pure]
    public function testApplication()
    {
        $application = new Application($this->config);
        $this->assertEquals('9021000131661052', $application->getApp()->getAppId());

        $request = $application->getRequest();
        $this->assertEquals('GET', $request->getMethod());

        $client = $application->getHttpClient();

        $data['grant_type'] = 'authorization_code';
        $data['code']       = '4b203fe6c11548bcabd8da5bb087a83b';
        $response = $client->post('alipay.system.oauth.token', $data);

        $body     = $response->getBody()->getContents();
        $data     = json_decode($body, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($data['error_response'], 'ok');
        $this->assertEquals(40002, $data['error_response']['code']);
    }

    public function testRequestApi()
    {
        $application = new Application($this->miniConfig);
        $client = $application->getHttpClient();

        $object['out_trade_no'] = 'test.123'.time();
        $object['total_amount'] = 99.0;
        $object['subject'] = '测试商品';
        $object['product_code'] = 'JSAPI_PAY';
        $object['buyer_id']     = '2088722020890152';
        $object['op_app_id']    = '9021000131659150';

        $requestData = ['biz_content' => json_encode($object)];
        $response = $client->post('alipay.trade.create', $requestData);
        $data     = json_decode($response->getbody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($data['alipay_trade_create_response'], 'ok');
        $this->assertEquals(10000, $data['alipay_trade_create_response']['code']);
    }
}