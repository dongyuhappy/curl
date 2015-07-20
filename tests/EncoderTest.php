<?php


/**
 * encoder test
 * Class EncoderTest
 */
class EncoderTest extends  PHPUnit_Framework_TestCase{

    /**
     *form-data
     * @throws \Simple\Curl\Exception\CurlException
     */
    public function testFormDataEncoder()
    {
        $data = array("name" => "Messi");
        $encoder = new \Simple\Curl\BodyEncoder\FormDataEncoder();
        $this->assertEquals($data, $encoder->toEncode($data));
    }


    /**
     * www-form-urlencoded
     * @throws \Simple\Curl\Exception\CurlException
     */
    public function testFormUrlEncoder(){
        $encoder = new Simple\Curl\BodyEncoder\FormUrlEncoder();
        $data = array("name"=>"Messi","age"=>27,"txt"=>"中文");
        $this->assertEquals("name=Messi&age=27&txt=".urlencode('中文'),$encoder->toEncode($data));

    }

    /**
     * get 编码
     * @throws \Simple\Curl\Exception\CurlException
     */
    public function testGetBody(){
        $encoder = new Simple\Curl\BodyEncoder\GetBodyEncoder();
        $data = array("name"=>"Messi","age"=>27,"txt"=>"中文");
        $this->assertEquals(http_build_query($data),$encoder->toEncode($data));
    }

    /**
     * json测试
     * @throws \Simple\Curl\Exception\CurlException
     */
    public function testJson(){
        $encoder = new Simple\Curl\BodyEncoder\JsonEncoder();
        $data = array("name"=>"Messi","age"=>27,"txt"=>"中文");
        $this->assertEquals(json_encode($data),$encoder->toEncode($data));
    }





}