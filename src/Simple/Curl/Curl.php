<?php

namespace Simple\Curl;


use Symfony\Component\EventDispatcher\EventDispatcher;
use Simple\Curl\Response\Response;
use Simple\Curl\Request\Request;
use Simple\Curl\Response\ResponseValue;

class Curl extends EventDispatcher
{

    /**
     * curl_init返回的操作对象
     * @var
     */
    private $ch = null;

    /**
     * 请求对象
     * @var Request
     */
    private $request;


    const  EVENT_BEFORE_REQUEST = "event_before_request";//发送请求前派发的事件
    const EVENT_AFTER_REQUEST = "event_after_request";//请求结束之后派发的事件

    /**
     * 初始化
     * @param Request $request
     */
    function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 发送一个curl请求
     * @param ResponseValue $responseValue 返回值格式化对象
     * @return Response
     */
    public function makeRequest(ResponseValue $responseValue)
    {
        if (!is_resource($this->ch)) {
            $this->createCURL();
        }

        $this->setHeader();//设置头
        $this->setCookie();//设置cookie
        $this->request->setTransportData();//设置要传输的数据
        $this->setOptions();//设置curl_options


        $this->dispatch(Curl::EVENT_AFTER_REQUEST);//请求前的事件


        $content = curl_exec($this->ch);//执行
        $this->dispatch(Curl::EVENT_AFTER_REQUEST);//请求结束后的事件

        $errorCode = curl_errno($this->ch);
        $response = new Response($this->request, $responseValue);
        $response->fillResponse($this->ch);

        if ($errorCode != 0) {
            //执行失败，直接返回错误
            $response->setErrorCode($errorCode);
            $response->setErrorMsg(curl_error($this->ch));
            return $response;
        }
        $response->getResponseValue()->toVo($content);//格式化返回的数据

        return $response;
    }


    /**
     *初始化一个curl的handler
     * @return void
     */
    private function createCURL()
    {
        $this->ch = curl_init();
    }


    /**
     * 设置curl的请求参数
     * @return void
     */
    private function setOptions()
    {
        $options = $this->request->getOptions();

        foreach ($options as $key => $val) {
            curl_setopt($this->ch, $key, $val);
        }
    }


    /**
     * 设置头
     * @return void
     */
    private function setHeader()
    {
        $headers = $this->request->getHeader();
        curl_setopt($this->ch, CURLOPT_HEADER, array_values($headers));
    }

    /**
     * 设置cookie
     * @return void
     */
    private function setCookie()
    {
        curl_setopt($this->ch, CURLOPT_COOKIE, $this->request->makeCookieString());
    }


    /**
     * 析构方法，关闭curl的链接
     */
    function __destruct()
    {
        $this->close();
    }


    /**
     * 关闭curl请求连接资源
     * @return void
     */
    public function close()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

}




