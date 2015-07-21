<?php

namespace Simple\Curl\Response;

use Simple\Curl\Exception\CurlException;
use Simple\Curl\Request\Request;

class Response
{
    /**
     * CURLINFO_HTTP_CODE
     * 返回码
     * @var int
     */
    private $httpCode;


    /**
     * CURLINFO_TOTAL_TIME
     * 本次请求花费的时间
     * @var int
     */
    private $totalTime = -1;


    /**
     * CURLINFO_SIZE_UPLOAD
     * 本次请求上传的数据(byte)
     * @var int
     */
    private $uploadSize = -1;


    /**
     * CURLINFO_SIZE_DOWNLOAD
     * 本次请求下载的数据(byte)
     * @var int
     */
    private $downloadSize = -1;

    /**
     * http Content-Type
     * @var string
     */
    private $contentType;

    /**
     * CURLINFO_PRIMARY_IP
     * 服务器IP
     * @var string
     */
    private $serverIP;

    /**
     * CURLINFO_PRIMARY_PORT
     * 端口号
     * @var int
     */
    private $serverPort;


    /**
     * CURLINFO_SPEED_DOWNLOAD
     * 平均下载速度
     * @var int
     */
    private $downloadSpeed;


    /**
     * CURLINFO_SPEED_UPLOAD
     * 平均上传速度
     * @var int
     */
    private $uploadSpeed;


    /**
     *错误信息描述
     * @var null| string
     */
    private $errorMsg = null;


    /**
     * 错误码 @see http://curl.haxx.se/libcurl/c/libcurl-errors.html
     * @var int
     */
    private $errorCode = 0;


    /**
     * 客户端IP
     * @var string
     */
    private $localIP;

    /**
     * 客户端端口
     * @var int
     */
    private $localPort;


    /**
     * @var ResponseValue
     */
    private $responseValue = null;

    /**
     * @param Request $request
     * @param ResponseValue $responseValue
     */
    function __construct(Request $request, ResponseValue $responseValue)
    {
        $this->responseValue = $responseValue;
    }


    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode
     */
    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @return int
     */
    public function getTotalTime()
    {
        return $this->totalTime;
    }

    /**
     * @param double $totalTime
     */
    public function setTotalTime($totalTime)
    {
        $this->totalTime = $totalTime;
    }

    /**
     * @return int
     */
    public function getUploadSize()
    {
        return $this->uploadSize;
    }

    /**
     * @param int $uploadSize
     */
    public function setUploadSize($uploadSize)
    {
        $this->uploadSize = $uploadSize;
    }

    /**
     * @return int
     */
    public function getDownloadSize()
    {
        return $this->downloadSize;
    }

    /**
     * @param int $downloadSize
     */
    public function setDownloadSize($downloadSize)
    {
        $this->downloadSize = $downloadSize;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getServerIP()
    {
        return $this->serverIP;
    }

    /**
     * @param string $serverIP
     */
    public function setServerIP($serverIP)
    {
        $this->serverIP = $serverIP;
    }

    /**
     * @return int
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }

    /**
     * @param int $serverPort
     */
    public function setServerPort($serverPort)
    {
        $this->serverPort = $serverPort;
    }

    /**
     * @return int
     */
    public function getDownloadSpeed()
    {
        return $this->downloadSpeed;
    }

    /**
     * @param int $downloadSpeed
     */
    public function setDownloadSpeed($downloadSpeed)
    {
        $this->downloadSpeed = $downloadSpeed;
    }

    /**
     * @return int
     */
    public function getUploadSpeed()
    {
        return $this->uploadSpeed;
    }

    /**
     * @param int $uploadSpeed
     */
    public function setUploadSpeed($uploadSpeed)
    {
        $this->uploadSpeed = $uploadSpeed;
    }

    /**
     * @return null|string
     */
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    /**
     * @param null|string $errorMsg
     */
    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return ResponseValue
     */
    public function getResponseValue()
    {
        return $this->responseValue;
    }

    /**
     * @param ResponseValue $responseValue
     */
    public function setResponseValue($responseValue)
    {
        $this->responseValue = $responseValue;
    }

    /**
     * @return string
     */
    public function getLocalIP()
    {
        return $this->localIP;
    }

    /**
     * @param string $localIP
     */
    public function setLocalIP($localIP)
    {
        $this->localIP = $localIP;
    }

    /**
     * @return int
     */
    public function getLocalPort()
    {
        return $this->localPort;
    }

    /**
     * @param int $localPort
     */
    public function setLocalPort($localPort)
    {
        $this->localPort = $localPort;
    }


    /**
     * 填充Response对象
     * @param $ch
     */
    public function fillResponse($ch)
    {
        $info = curl_getinfo($ch);
        $this->setHttpCode(intval($info["http_code"]));//http状态码
        $this->setTotalTime($info['total_time']);//花费的时间
        $this->setUploadSize($info['size_upload']);//发送的数据大小
        $this->setDownloadSize($info['size_download']);//返回数据的大小
        $this->setContentType($info['content_type']);
        $this->setServerIP($info['primary_ip']);//服务器的IP
        $this->setServerPort($info['primary_port']);//服务端口
        $this->setDownloadSpeed($info['speed_download']);//返回的速度
        $this->setUploadSpeed($info['speed_upload']);//发送数据的速度
        $this->setLocalIP($info['local_ip']);//客户端IP
        $this->setLocalPort($info['local_port']);//客户端端口
    }

    /**
     * 检查curl请求的结果状态
     * @throws CurlException
     */
    public function check()
    {
        if ($this->getErrorCode() != 0) {
            throw new CurlException("Curl请求错误:" . $this->getErrorCode() . "(" . $this->getErrorMsg() . ")");
        }
        $httpCode = $this->getHttpCode();
        if (in_array(intval($httpCode / 100), array(4, 5))) {
            throw new CurlException("http状态码错误：" . $httpCode);
        }
    }


}

