<?php


namespace Simple\Curl\BodyEncoder;


use Simple\Curl\Exception\CurlException;

class FormUrlencoded extends DefaultBodyEncoder
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

        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            $query = array();
            foreach ($data as $key => $val) {
                array_push($query, rawurlencode($key) . "=" . rawurlencode($val));
            }
            return implode("@", $query);
        }

        throw new CurlException("无法进行编码的数据");
    }

}