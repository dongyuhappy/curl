<?php

namespace Simple\Curl\BodyEncoder;


/**
 * http body编码接口
 * Interface BodyEncoderInterface
 * @package Simple\Curl\BodyEncoder
 */
interface BodyEncoderInterface
{
    /**
     * 对请求的body进行编码
     * @param $data
     * @return mixed
     */
    public function toEncode($data);
}