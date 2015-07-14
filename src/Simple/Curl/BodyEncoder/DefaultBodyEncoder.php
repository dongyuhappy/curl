<?php

namespace Simple\Curl\BodyEncoder;


abstract class DefaultBodyEncoder implements BodyEncoderInterface
{


    /**
     * object
     * @param $data
     * @return mixed
     */
    protected function objectToArray($data)
    {
        if (is_object($data) && method_exists($data, "toArray")) {
            //如果有toArray方法就嗲偶偶那个toArray方法
            $data = call_user_func_array(array($data, "toArray"), array());
        }
        return $data;
    }


}