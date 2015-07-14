<?php

namespace Simple\Curl\BodyEncoder;


use Simple\Curl\Exception\CurlException;

class JsonEncoder extends DefaultBodyEncoder
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
            return json_encode($data);
        }
        if (is_string($data)) {
            return $data;
        }

        throw new CurlException('无法进行编码的数据');

    }

}