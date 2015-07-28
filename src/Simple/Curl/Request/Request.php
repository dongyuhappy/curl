<?php

namespace Simple\Curl\Request;


use Simple\Curl\BodyEncoder\FormDataEncoder;
use Simple\Curl\BodyEncoder\FormUrlEncoder;
use Simple\Curl\BodyEncoder\GetBodyEncoder;
use Simple\Curl\BodyEncoder\JsonEncoder;
use Simple\Curl\BodyEncoder\RequestBodyEncodeType;
use Simple\Curl\Support\HttpMethod;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Simple\Curl\Exception\CurlException;


class Request extends EventDispatcher
{
    const  EVENT_OPTIONS = "CURL_OPTIONS_";//设置curl的option派发的事件名称

    const EVENT_HEADER = "CURL_HEADER_";//设置http请求头信息派发的时间名称



    const EVENT_RESET_COOKIES = "EVENT_RESET_COOKIES";





    /**
     *
     * @var array
     */
    private $header = array();

    /**
     * 执行请求的URL
     * @var string
     */
    private $url;


    /**
     * http 请求body的编码方式
     * @var string
     */
    private $requestBodyEncoder;


    /**
     * http 方法
     * @var string
     */
    private $httpMethod;

    /**
     * cookie
     * @var array
     */
    private $cookies = array();


    /**
     * 要发送的数据
     * @var array
     */
    private $params = array();


    /**
     * curl设置的参数
     * @var array
     */
    private $options = array(
        CURLOPT_HEADER => false,
        CURLOPT_CONNECTTIMEOUT => 5,//连接超时时间的设定(单位：秒)
        CURLOPT_RETURNTRANSFER => true,//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    );


    /**
     *
     * @param $url
     * @param string $httpMethod http方法 默认为POST
     * @param string $bodyEncoder http body的编码方式
     * @param array $header 默认是disable 100-continue
     * @param array $options curl设置参数
     * @param array $cookies
     * @param array $params
     * @throws CurlException
     */
    function __construct($url, $httpMethod = "POST", $bodyEncoder = RequestBodyEncodeType::WWW_FORM_URLENCODED, $header = array('Expect' => ''), $options = array(), $cookies = array(), $params = array())
    {


        if (RequestBodyEncodeType::isSupport($bodyEncoder) == false) {
            throw new CurlException("无法支持的http body编码方式" . $bodyEncoder);
        }
        $this->requestBodyEncoder = $bodyEncoder;

        $this->addHeaders($header);
        $this->url = $url;
        $this->setHttpMethod($httpMethod);
        $this->addCookies($cookies);
        $this->addParams($params);
        $this->addOptions($options);
    }


    /**
     * http不同方法的一些特性
     * @param $httpMethod
     * @throws CurlException
     */
    private function setHttpMethodFuture($httpMethod)
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, $httpMethod);//请求的http方法
        $process = array();

        $process[HttpMethod::GET] = function () {
            $this->setOption(CURLOPT_HTTPGET, true);
        };

        $process[HttpMethod::POST] = function () {
            $this->setOption(CURLOPT_POST, 1);
        };
        $process[HttpMethod::DELETE] = function(){
            $this->setOption(CURLOPT_CUSTOMREQUEST,HttpMethod::DELETE);
        };
        if (isset($process[$httpMethod])) {
            $process[$httpMethod]();
        }
    }

    /**
     * @param string $httpMethod
     * @throws CurlException
     */
    public function setHttpMethod($httpMethod)
    {
        if (HttpMethod::isSupport($httpMethod) == false) {
            throw new CurlException('未知的http方法' . $httpMethod);
        }
        $this->setHttpMethodFuture($httpMethod);
        $this->httpMethod = $httpMethod;
    }


    /**
     *
     * @param $key
     * @param $val
     * @return $this
     */
    public function setParam($key, $val)
    {
        $this->params[$key] = $val;
        return $this;
    }


    /**
     *
     * @param array $data
     * @return $this
     */
    public function addParams(array $data)
    {
        foreach ($data as $key => $val) {
            $this->setParam($key, $val);
        }
        return $this;
    }


    /**
     * 新增cookie
     * @param $key
     * @param $val
     * @return $this
     */
    public function setCookie($key, $val)
    {
        $this->cookies[$key] = $val;
        return $this;
    }


    /**
     * 新增数据cookies
     * @param array $cookies
     * @return $this
     */
    public function addCookies(array $cookies)
    {
        foreach ($cookies as $key => $val) {
            $this->setCookie($key, $val);
        }
        return $this;
    }


    /**
     * 设置curl的options参数
     * @param $key
     * @param $val
     * @return $this
     * @throws CurlException
     */
    public function setOption($key, $val)
    {
        if (in_array($key, array(CURLOPT_HTTPHEADER, CURLOPT_COOKIE))) {
            throw new CurlException("无法设置" . $key);
        }
        $this->options[$key] = $val;
        $this->dispatch(Request::EVENT_OPTIONS . $key);//派发一个事件
        return $this;
    }

    /**
     *
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        foreach ($options as $key => $val) {
            $this->setOption($key, $val);
        }
        return $this;
    }

    /**
     * 派发一个事件
     * @param string $eventName
     * @param Event $event
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {

        return parent::dispatch($eventName, $event);
    }


    /**
     * 设置请求头
     * @param $key
     * @param $val
     * @return $this
     */
    public function setHeader($key, $val)
    {
        $this->header[$key] = $key . ":" . $val;
        $this->dispatch(Request::EVENT_HEADER . $key);
        return $this;
    }


    /**
     *
     * @param array $headers
     * @return $this
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $key => $val) {
            $this->setHeader($key, $val);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestBodyEncoder()
    {
        return $this->requestBodyEncoder;
    }


    /**
     * @param $requestBodyEncoder
     * @throws CurlException
     */
    public function setRequestBodyEncoder($requestBodyEncoder)
    {
        if (RequestBodyEncodeType::isSupport($requestBodyEncoder)) {
            throw new CurlException("不支持的http消息主体编码方式" . $requestBodyEncoder);
        }
        $this->requestBodyEncoder = $requestBodyEncoder;
    }


    /**
     * 对Cookie进行编码
     * @return string
     */
    public function makeCookieString()
    {

        $cookie_string = array();
        foreach ($this->cookies as $key => $value) {
            array_push($cookie_string, $key . '=' . $value);
        }
        $cookie_string = join('; ', $cookie_string);
        return $cookie_string;
    }

    /**
     * get 参数的编码
     * @return mixed
     * @throws CurlException
     */
    private function  httpGetMakeQueryString()
    {
        $getEncoder = new GetBodyEncoder();
        return $getEncoder->toEncode($this->params);
    }


    /**
     * post参数编码
     * @return string
     * @throws CurlException
     */
    private function httpPostMakeQueryString()
    {
        $bodyEncoder = array();//http body编码器列表

        //www-form-urlencoded
        $bodyEncoder[RequestBodyEncodeType::WWW_FORM_URLENCODED] = function () {
            $encoder = new FormUrlEncoder();
            return $encoder->toEncode($this->params);
        };


        //json
        $bodyEncoder[RequestBodyEncodeType::JSON] = function () {
            $encoder = new JsonEncoder();
            return $encoder->toEncode($this->params);
        };

        //form-data
        $bodyEncoder[RequestBodyEncodeType::FORM_DATA] = function(){
            $encoder = new FormDataEncoder();
            return $encoder->toEncode($this->params);
        };

        if (isset($bodyEncoder[$this->requestBodyEncoder]) == false) {
            throw new CurlException("暂无支持的body编码类型" . $this->requestBodyEncoder);
        }

        return $bodyEncoder[$this->requestBodyEncoder]();
    }


    /**
     * 对参数进行编码
     * @return string
     * @throws CurlException
     */
    public function makeQueryString()
    {


        $support = array();//支持的http方法

        $process[HttpMethod::DELETE] = $process[HttpMethod::PUT] = $support[HttpMethod::GET] = function () {
            return $this->httpGetMakeQueryString();
        };

        $support[HttpMethod::DELETE] = $support[HttpMethod::POST] = function () {
            return $this->httpPostMakeQueryString();
        };



        if (isset($support[$this->httpMethod]) == false) {
            throw new CurlException('暂不支持的http方法' . $this->httpMethod);
        }

        return $support[$this->httpMethod]();

    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * 设置要传输的数据
     */
    public function setTransportData()
    {
        $process = array();

        //http get
        $url = $this->getUrl();
         $process[HttpMethod::PUT] = $process[HttpMethod::GET] = function () use ($url) {

            $this->setOption(CURLOPT_URL, $url . '?' . $this->makeQueryString());
        };


        $process[HttpMethod::DELETE] =$process[HttpMethod::POST] = function () use ($url) {
            $this->setOption(CURLOPT_URL, $url);
            $this->setOption(CURLOPT_POSTFIELDS, $this->makeQueryString());
        };

        if (isset($process[$this->getHttpMethod()])) {
            $process[$this->getHttpMethod()]();
        }
    }

    /**
     * 重置先关信息
     * @return void
     */
    public function reset()
    {
        $this->resetCookies();
        $this->resetParams();
        $this->resetOptions();
        $this->resetHeader();
    }


    /**
     *
     * @return void
     */
    public function resetHeader(){
        $this->header = array();

    }

    /**
     * @return void
     */
    public function resetOptions(){
        $this->options = array();
    }


    /**
     * @return void
     */
    public function resetParams(){
        $this->params = array();
    }



    /**
     * @return void
     */
    public function resetCookies(){
        $this->cookies = array();
    }

    /**
     * 删除某个cookie
     * @param $key
     */
    public function removeCookie($key)
    {
        if (isset($this->cookies[$key])) {
            unset($this->cookies[$key]);
        }
    }

    /**
     * @param $key
     */
    public function removeParam($key)
    {
        if(isset($this->params[$key])){
            unset($this->params[$key]);
        }
    }

    /**
     * @param $key
     */
    public function removeOption($key){
        if(isset($this->options[$key])){
            unset($this->options[$key]);
        }
    }

    /**
     *
     * @param $key
     */
    public function removeHeader($key){
        if(isset($this->header[$key])){
            unset($this->header[$key]);
        }
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }



}