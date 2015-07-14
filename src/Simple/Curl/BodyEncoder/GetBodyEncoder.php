<?php

namespace Simple\Curl\BodyEncoder;


use Simple\Curl\Exception\CurlException;

class GetBodyEncoder extends DefaultBodyEncoder
{
    /**
     * 对请求的body进行编码
     * @param $data
     * @return mixed
     * @throws CurlException
     */
    public function toEncode($data)
    {

        $data = $this->objectToArray($data);

        if (is_array($data)) {
            return http_build_query($data);
        }

        if (is_string($data)) {
            return $data;
        }

        throw new CurlException('无法对数据进行编码');
    }

}