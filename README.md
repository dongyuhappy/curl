## 简介

simple-curl是一个简单的把curl操作的类，封装了常用的curl操作功能，让你以更加OO的方式来使用php的curl。php的curl本身依赖以[libcurl](http://curl.haxx.se/)，本质上来时libcurl支持的各种协议simple-curl都支持，*但是,simple-curl更加侧重于对http协议的curl参数的一些封装。*


## 核心思想
- 请求参数都被封装在`Request`对象里面了。
- curl执行的结果的信息都封装在了`Response`对象里面。结果信息主要分为两类：
      - meta信息，例如请求花费的时间之类的。
      - curl请求服务器响应的结果信息。

- 对于服务器返回的结果信息，必须实现`ResponseValue`接口，该接口里面有一个`toVo`方法，用来解析服务端返回的数据。
- 具体执行curl请求的相关操作被放在了`Curl`类里面。换句话说`Curl`就是个调度类。

## 快速使用
```php

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


```


## 关于事件
- `Curl`对象上面在执行curl前后各派发了一个事件
- `Request`对象上面的事件都是以设置参数的名字作为事件名字的结尾
    - 设置Option派发
    - 设置header派发

## 支持的http方法
目前支持的http方法有：GET,POST,DELETE,PUT。

## 关于post提交数据的方式

http协议关于http请求只定义了三个部分：状态行，头信息，消息主体。关于消息主体部分并没有规定如何进行编码。那么客户端post到服务端的数据，服务端是如何解析的呢？post到服务端的数据有这么几种方式进行提交：
- form-data
- x-www-form-urlencoded
- raw
- binary

### form-data

我们使用表单上传文件的时候就是采用这种用方式

### x-www-form-urlencoded 

form表单默认提交数据的方式，对url的key和value都进行urlencode的编码


### raw
原始的ASCII提交，里面可以是任何的数据，但是你这数据提交过去过去了得告诉服务端如何解析，所以以这种方式提交数据的时候尽量告诉服务端如何解析你提交过去的数据。目前raw形式的编码支持json格式











