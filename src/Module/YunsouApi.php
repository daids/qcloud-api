<?php
namespace Daids\QcloudApi\Module;

use Illuminate\Contracts\Config\Repository;
use GuzzleHttp\Client;

class YunsouApi
{
    private $appId;
    private $secretId;
    private $secretKey;

    public function __construct(Repository $config)
    {
        $this->appId = array_get($config, 'qcloud.yunsou.appId', '');
        $this->secretId = array_get($config, 'qcloud.base.secretId', '');
        $this->secretKey = array_get($config, 'qcloud.base.secretKey', '');
    }

    public function getSign($params)
    {
        $plainText = '';
        ksort($params);
        foreach ($params as $key => $value) {
            $plainText .= str_replace('_', '.', $key).'='.str_replace('_', '.', $value).'&';
        }
        $plainText = 'GETyunsou.api.qcloud.com/v2/index.php?'.rtrim($plainText, '&');
        return base64_encode(hash_hmac('SHA1', $plainText, $this->secretKey, true));
    }

    public function add($data)
    {
        $params = [
            'Action' => 'DataManipulation',
            'Nonce' => rand(),
            'Region' => 'sh',
            'SecretId' => $this->secretId,
            'Timestamp' => time(),
            'op_type' => 'add',
            'appId' => $this->appId
        ];
        foreach ($data as $key => $value) {
            $params['contents.0.'.$key] = $value;
        }
        $params['Signature'] = $this->getSign($params);

        $url = 'https://yunsou.api.qcloud.com/v2/index.php?'.http_build_query($params);

        $httpClient = new Client();

        try {
            $response = $httpClient->get($url);
        } catch (Exception $e) {
            return ['result' => false, 'message' => '访问远程出错！'];
        }
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody(), true);
            if (isset($result['code']) && !$result['code']) {
                return ['result' => true];
            }
            return ['result' => false, 'message' => '添加失败！'];
        }
        return ['result' => false, 'message' => '访问远程未成功！'];
    }

    public function search($keyword, $page, $perPage)
    {
        $params = [
            'Action' => 'DataSearch',
            'Nonce' => rand(),
            'Region' => 'sh',
            'SecretId' => $this->secretId,
            'Timestamp' => time(),
            'appId' => $this->appId,
            'search_query' => $keyword,
            'page_id' => $page,
            'num_per_page' => $perPage
        ];

        $params['Signature'] = $this->getSign($params);

        $url = 'https://yunsou.api.qcloud.com/v2/index.php?'.http_build_query($params);

        $httpClient = new Client();

        try {
            $response = $httpClient->get($url);
        } catch (Exception $e) {
            return ['result' => false, 'message' => '访问远程出错！'];
        }
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody(), true);
            if (isset($result['code']) && !$result['code']) {
                return ['result' => true, 'data' => $result['data']];
            }
            return ['result' => false, 'message' => '添加失败！'];
        }
        return ['result' => false, 'message' => '访问远程未成功！'];
        
    }
}
