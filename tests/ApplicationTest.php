<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Tests;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Kernel\App;
use Honghm\EasyAlipay\Kernel\Contract\AlipayResponseInterface;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;
use JetBrains\PhpStorm\Pure;

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

    /**
     * @throws InvalidConfigException
     */
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
        $data = $response->getData();
        $this->assertSame('40002', $data['error_response']['code'], 'ok');
    }
}