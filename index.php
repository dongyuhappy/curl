<?php
require './vendor/autoload.php';

/**
 * 保存返回值的对象
 * Class ValueObject
 */
class ValueObject implements \Simple\Curl\Response\ResponseValue
{
    private $html;

    /**
     * 格式化返回的数据
     * @param $data
     * @return mixed
     */
    public function toVo($data)
    {
        $this->html = $data;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }


}

$url = "http://www.baidu.com";
//创建Request对象
$request = new Simple\Curl\Request\Request($url, \Simple\Curl\Support\HttpMethod::GET);
$curl = new \Simple\Curl\Curl($request);
$response = $curl->makeRequest(new ValueObject());

if ($response->getErrorCode() != 0) {
    //请求出现错误
    throw new \Simple\Curl\Exception\CurlException($response->getErrorMsg());
}
if (in_array(intval($response->getHttpCode() / 100), array(4, 5))) {
    //服务器的http 状态码为4xx或者5xx
    throw new \Simple\Curl\Exception\CurlException($response->getHttpCode());
}

$vo = $response->getResponseValue();
if($vo instanceof ValueObject){
    echo $vo->getHtml();

}
