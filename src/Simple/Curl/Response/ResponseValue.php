<?php

namespace Simple\Curl\Response;


/**
 * 执行CURL的返回值
 * Interface ResponseValue
 * @package Simple\Curl
 */
interface ResponseValue
{

    /**
     * 格式化返回的数据
     * @param $data
     * @return mixed
     */
    public function toVo($data);
}