<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests\Pay;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidParamException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidResponseException;
use Honghm\EasyAlipay\Kernel\Notify;
use Honghm\EasyAlipay\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class PayTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    public function testAlipayTradePagePayApi()
    {
        $application = new Application($this->config);
        $client = $application->getHttpClient();

        $object['_notify_url']  = 'https://channel.baidu.cn/v2/pay/notify';
        $object['out_trade_no'] = 'test.123'.time();
        $object['total_amount'] = 99.0;
        $object['subject']      = 'test.123'.time();
        $object['product_code'] = 'FAST_INSTANT_TRADE_PAY';
        $requestData = ['biz_content' => $object];

        $response = $client->getPageResponse('alipay.trade.page.pay', $requestData);

        var_dump($response->getHeaders());

        $this->assertEquals(200, $response->getStatusCode());
    }

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

        $requestData = ['biz_content' => $object];
        $response = $client->post('alipay.trade.create', $requestData);
        $data     = $response->getData();

        $this->assertSame('10000', $data['code'], 'ok');
        $this->assertSame($object['out_trade_no'], $data['out_trade_no'], 'ok');
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
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws InvalidResponseException
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function testAlipayNotifyApi()
    {
        $application = new Application($this->config);

        $params['gmt_create'] = '2023-11-27+16%3A55%3A35';
        $params['charset'] = 'utf-8';
        $params['gmt_payment'] = '2023-11-27+16%3A55%3A42';
        $params['notify_time'] = '2023-11-27+16%3A55%3A43';
        $params['subject'] = 'test.1231701075317';
        $params['sign'] = 'VpXQzaXaNl4nW4Nrbibn5ajxVaF3I74NDvJNAeFcv4U6PXIrdc+tH8WyqNybnRCOsHa+qMZR2bgTKsqU/fzeTgcZIElsEFrTwbb6cWmyJSUe749XodVpmTYq094rGcO9SOXFXrKgcDO/2ycakleS6W6t3MkGe87Pu+DRmVcj4K/FHB0p+eFS9Cc8Y4z23a2M0l5/sIaxRYKCLWpfFiedQvWzJ/YiyaDBBhvOKIMGbuhSEvmDfDVKBKLrTBhrzUUOQ85g9p20Sm4/+an9m/jCRrDu2oX3deEIVpk34DxvGdEly45lxr/fkEuY5Lgi8kNHl9j9V+3g6xbVJDSbLlf84A==';
        $params['buyer_id'] = '2088722020890152';
        $params['invoice_amount'] = '99.00';
        $params['version'] = '1.0';
        $params['notify_id'] = '2023112701222165543090150501296232';
        $params['fund_bill_list'] = '%5B%7B%22amount%22%3A%2299.00%22%2C%22fundChannel%22%3A%22ALIPAYACCOUNT%22%7D%5D';
        $params['notify_type'] = 'trade_status_sync';
        $params['out_trade_no'] = 'test.1231701075317';
        $params['total_amount'] = '99.00';
        $params['trade_status'] = 'TRADE_SUCCESS';
        $params['trade_no'] = '2023112722001490150501275983';
        $params['auth_app_id'] = '9021000131661052';
        $params['receipt_amount'] = '99.00';
        $params['point_amount'] = '0.00';
        $params['buyer_pay_amount'] = '99.00';
        $params['app_id'] = '9021000131661052';
        $params['sign_type'] = 'RSA2';
        $params['seller_id'] = '2088721020867692';
        $request = new ServerRequest('POST', 'https://baidu.com');
        $request = $request->withParsedBody($params);

        $application->setRequest($request);
        $notify = new Notify($application);

        $data = $notify->callback();
        $this->assertNotNull($data, 'okok');
        $this->assertSame($params['seller_id'], $data['seller_id'], 'success');
    }
}