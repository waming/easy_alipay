<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Kernel\App;
use Honghm\EasyAlipay\Kernel\Contract\AlipayResponseInterface;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;

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

        $responseInterface = $response->getResponse(); //获取原始响应
        $this->assertNotNull($responseInterface->getBody()->getContents());

        $rawData = $response->getRawData(); //json_decode 后的数据
        $this->assertSame('40002', $rawData['error_response']['code'], 'ok');

        $result  = $response->getData();    //json_decode后去除验签的数据
        $this->assertSame('40002', $result['code'], 'ok');
    }

    public function testApi()
    {
        $application = new Application($this->config);
        $client = $application->getHttpClient();

        $data = [
            'user_id' => '123123',
            'budget_code' => "1111",
            'partner_biz_no' => date('YmdHis').random_int(1000, 9999),
            'point_amount' => 1,
        ];

        $response = $client->post('alipay.user.alipaypoint.send', ['biz_content' => $data]);
        $data = $response->getData();
        $this->assertNotNull($data, 'not empty');
        $this->assertSame($data['code'], '40006', 'ok');
    }
}