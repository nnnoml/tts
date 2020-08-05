<?php
namespace Charis\TTS\Plug;

//http://tools.bugscaner.com/tts/
class BugscanerTTS implements TTSinterface {
    private $url = 'http://tools.bugscaner.com/api/tts/';

    private function __construct(){
    }
    public static function getInstance(){
        return new self;
    }
    public function init($app_id,$app_key,$text){
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        //first get key
        $yusu = 3;
        $fasheng = 0;
        $url1 = $this->url.'?range='.$msectime;

        $res = $this->httpPost($url1,['text'=>$text,'yusu'=>$yusu,'fasheng'=>$fasheng]);

        $foo = json_decode($res,true)['video'];
        $format_foo = $this->convertUrlQuery($foo);

        $key = $format_foo['key'];
        $audio = $format_foo['audio'];
        //second get voice
        $url2 = $this->url.'?text='.$text.'&yusu='.$yusu.'&fayin='.$fasheng.'&key='.$key.'&audio='.$audio;
        $res = $this->httpGet($url2);
        return $res;
    }

    public function formatVoice($response)
    {
        return base64_encode($response);
    }

    public function saveVoice($response,$path,$file_name){
        $speech = $this->formatVoice($response);
        file_put_contents($path.$file_name.'.mp3', base64_decode($speech));
    }

    function httpPost($url,$post_data){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

    function httpGet($url)
    {
        $url = str_replace(' ', '%20', $url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //如果有跳转 循环跟进
        $res = curl_exec($curl);

        curl_close($curl);
        return $res;
        return base64_encode($res);
    }


    function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param)
        {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }

    function getUrlQuery($array_query)
    {
        $tmp = array();
        foreach($array_query as $k=>$param)
        {
            $tmp[] = $k.'='.$param;
        }
        $params = implode('&',$tmp);
        return $params;
    }
}