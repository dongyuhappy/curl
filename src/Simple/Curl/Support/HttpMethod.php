<?php

namespace Simple\Curl\Support;


/**
 *
 * http协议支持的方式
 * Class HttpMethod
 * @package Simple\Curl
 */
class HttpMethod
{
    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const DELETE = "DELETE";
    const PATCH = "PATCH";
//    const HEAD = "HEAD";
//    const OPTIONS = "OPTIONS";


    /**
     * 是否支持的方法
     * @param $httpMethod
     * @return bool
     */
    public static function isSupport($httpMethod)
    {
        $all = array(
            self::GET,
            self::POST,
            self::PUT,
            self::DELETE,
            self::PATCH,
//            self::HEAD,
//            self::OPTIONS,
        );
        return in_array($httpMethod, $all);
    }



}