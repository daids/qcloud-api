# qcloud-api
qcloud api for laravel5

### 云搜API

* 配置

```php
//config/app.php
...
Daids\QcloudApi\QcloudApiServicePorvider::class
...
'Yunsou' => 'Daids\QcloudApi\Facades\Yunsou'
```
```shell
php artisan vendor:publish
```
```php
//config/qcloud.php  填写你的配置项
'base' => [
	'appId' => '',
	'secretId' => '',
	'secretKey' => '',
],
'yunsou' => [
	'appId' => '' //云搜业务appId
]
```

* 添加记录
  * `Yunsou::add($data)` $data为上传数据，结构以配置为主，
```php
//例如：
$data = [
	'id' => 1,
	'title' => 'test'
]
```

* 搜索记录
  * `Yunsou::search($keyword, $page, $perPage)` $page从0开始
```php
//返回结果
$result = [
		'result' => true, 
	'data' =>
		"cost_time": 19,
		"display_num": 2,
		"echo": "",
		"eresult_num": 2,
		"result_list": [
			{
				"doc_id": "1",
				"doc_meta": "{
					"id": "1",
					"title": "test"
				}",
				"l2_score": 0,
				"search_debuginfo": ""
			},
			{
				"doc_id": "2",
				"doc_meta": "{
					"id": "2",
					"title": "test2"
				}",
				"l2_score": 0,
				"search_debuginfo": ""
			}
		],
		"result_num": 2,
		"seg_list": [
			{
				"seg_str": "test"
			},
			{
				"seg_str": "test2"
			}
		]
	];
```
