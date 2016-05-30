<?php
namespace Daids\QcloudApi\Module;

use Illuminate\Contracts\Config\Repository;
use GuzzleHttp\Client;

class CosApi
{
    private $appId;
    private $secretId;
    private $secretKey;
    private $server = 'http://web.file.myqcloud.com/files/v1/';
    private $httpClient;

    public function __construct(Repository $config)
    {
        $this->appId = array_get($config, 'qcloud.appId', '');
        $this->secretId = array_get($config, 'qcloud.secretId', '');
        $this->secretKey = array_get($config, 'qcloud.secretKey', '');
        $this->httpClient = new Client(['timeout' => 30]);
    }

    public function getSign($bucketName, $expired = 0, $fileId = null)
    {
        $now = time();
        $rdm = rand();
        $plainText = "a={$this->appId}&k={$this->secretId}&e=$expired&t=$now&r=$rdm&f=$fileId&b=$bucketName";
        $bin = hash_hmac('SHA1', $plainText, $this->secretKey, true);
        $sign = base64_encode($bin.$plainText);
        return $sign;
    }

    public function urlEncode($path)
    {
        return str_replace('%2F', '/', rawurlencode($path));
    }

    public function generateResUrl($bucketName, $dstPath)
    {
        return $this->server.$this->appId.'/'.$bucketName.'/'.$dstPath;
    }

    public function upload($srcPath, $bucketName, $dstPath)
    {
        $srcPath = realpath($srcPath);
        if (! file_exists($srcPath)) {
            return ['code' => 1, 'message' => '文件不存在'];
        }
        $expired = time() + 360;
        $dstPath = $this->urlEncode($dstPath);
        $url = $this->generateResUrl($bucketName, $dstPath);
        $sign = $this->getSign($bucketName, $expired);

        try {
            $response = $this->httpClient->post($url, [
                'multipart' => [
                    [
                        'name'     => 'op',
                        'contents' => 'upload'
                    ],
                    [
                        'name'     => 'filecontent',
                        'contents' => fopen($srcPath, 'r')
                    ]
                ],
                'headers' => [
                    'Authorization' => $sign
                ]
            ]);
        } catch (\Exception $e) {
            return ['code' => 2, 'message' => '接口请求异常'];
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function deleteFile($bucketName, $dstPath)
    {
        $dstPath = $this->urlEncode($dstPath);
        $url = $this->generateResUrl($bucketName, $dstPath);
        try {
            $response = $this->httpClient->post($url, [
                'body' => json_encode(['op' => 'delete'])
            ]);
        } catch (\Exception $e) {
            return ['code' => 2, 'message' => '接口请求异常'];
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function createDir($bucketName, $dirName)
    {
        $dirName = $this->urlEncode($dirName);
        $url = $this->generateResUrl($bucketName, $dirName);
        try {
            $response = $this->httpClient->post($url, [
                'body' => json_encode(['op' => 'create'])
            ]);
        } catch (\Exception $e) {
            return ['code' => 2, 'message' => '接口请求异常'];
        }

        return json_decode((string)$response->getBody(), true);
    }

    public function deleteDir($bucketName, $dirName)
    {
        $dirName = $this->urlEncode($dirName);
        $url = $this->generateResUrl($bucketName, $dirName);
        try {
            $response = $this->httpClient->post($url, [
                'body' => json_encode(['op' => 'delete'])
            ]);
        } catch (\Exception $e) {
            return ['code' => 2, 'message' => '接口请求异常'];
        }

        return json_decode((string)$response->getBody(), true);
    }
}
