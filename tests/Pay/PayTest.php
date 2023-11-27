<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests\Pay;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Common\Exception\InvalidConfigException;
use Honghm\EasyAlipay\Tests\TestCase;

class PayTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    public function testAlipayTradePagePayApi()
    {
        $application = new Application($this->config);
        $client = $application->getHttpClient();

        $object['_notify_url']  = 'https://channel.luketop.cn/v2/pay/notify';
        $object['out_trade_no'] = 'test.123'.time();
        $object['total_amount'] = 99.0;
        $object['subject']      = 'test.123'.time();
        $object['product_code'] = 'FAST_INSTANT_TRADE_PAY';
        $requestData = ['biz_content' => $object];

        $response = $client->getPageResponse('alipay.trade.page.pay', $requestData);

        var_dump($response->getHeaders());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws InvalidConfigException
     */
    public function testAlipayTradeCreateApi()
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

    /**
     * @throws InvalidConfigException
     */
    public function testAlipayTradeWapPayApi()
    {
        $application = new Application($this->config);
        $client = $application->getHttpClient();
        $object['out_trade_no'] = 'test.123'.time();
        $object['total_amount'] = 99.0;
        $object['subject']      = '测试商品';
        $object['product_code'] = 'QUICK_WAP_WAY';

        $response = $client->getPageResponse('alipay.trade.wap.pay', $object, 'POST');
        $body     = $response->getBody()->getContents();
        file_put_contents( BAST_PATH .'/test.html', $body);
        $this->assertEquals(200, $response->getStatusCode());
    }
}