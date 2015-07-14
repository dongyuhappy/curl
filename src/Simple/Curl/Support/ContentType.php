<?php

namespace Simple\Curl\Support;


class ContentType
{
    const TEXT = "text/plain";
    const HTML = "text/html";
    const JSON = "application/json";
    const JAVASCRIPT = "application/javascript";


    /**
     * 是否支持的content-type
     * @param $contentType
     * @return bool
     */
    public static function isSupport($contentType)
    {
        $all = array(self::TEXT, self::HTML, self::JSON, self::JAVASCRIPT);
        return in_array($contentType, $all);
    }
}