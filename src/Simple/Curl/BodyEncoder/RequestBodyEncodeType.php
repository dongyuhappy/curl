<?php

namespace Simple\Curl\BodyEncoder;


/**
 * http请求消息主体的编码方式
 * http协议只定义了http请求的三个部分：状态行 请求头 消息主体。但是并没用定义消息主体的编码格式
 *
 * Class RequestBodyEncodeType
 * @package Simple\Curl
 */
class RequestBodyEncodeType
{

    /**
     * <p>
     * 浏览器的原生 form 表单，如果不设置 enctype 属性，
     * 那么最终就会以 application/x-www-form-urlencoded 方式提交数据
     * </p>
     *
     * <p>编码方式为：key1=val1&key2=val2 ,value要进行urlencode编码</p>
     */
    const WWW_FORM_URLENCODED = "application/x-www-form-urlencoded";

    /**
     * @see http://www.ietf.org/rfc/rfc1867.txt
     * 一般上传资源文件到服务器都采用这种方式。
     * <p>
     * 首先会生成一个boundary，然后消息体按照字段划分为多个结构类似的部分，
     * 每个部分以boundary开始，紧接着是内容描述，然后是回车
     *
     * </p>
     *
     */
    const  FORM_DATA = "multipart/form-data";


    /**
     * 自定义消息主体，主要包含下面定义的那些
     */
    const BINARY = "binary";


    //下面这些方式都是raw形式

    /**
     * json
     */
    const JSON = "application/json";

    /**
     * xml
     */
    const XML = "application/xml";


    /**
     * js
     */
    const JAVASCRIPT = "application/javascript";

    /**
     * html
     */
    const HTML = "text/html";


    /**
     * 是否支持的的http消息主体编码方式
     * @param $bodyEncoder
     * @return bool
     */
    public static function isSupport($bodyEncoder)
    {
        $all = array(
            self::WWW_FORM_URLENCODED,
            self::FORM_DATA,
            self::BINARY,
            self::JSON,
//            self::XML,
//            self::JAVASCRIPT,
//            self::HTML
        );
        return in_array($bodyEncoder, $all);
    }

}