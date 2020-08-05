<?php
namespace Charis\TTS\Plug;

class TencentTTS implements TTSinterface {
    private $url = 'https://api.ai.qq.com/fcgi-bin/aai/aai_tts';
    private function __construct(){
    }
    public static function getInstance(){
        return new self;
    }
    public function init($app_id,$app_key,$text){
        $params = array(
            'app_id'=>$app_id,
            'time_stamp' => time(),
            'nonce_str'=>uniqid("{$app_id}_"),
            'text' => $text,
            'speaker'=>7,
            'format'=>3,
            'volume'=>10,
            'speed'=>100,
            'aht'=>0,
            'apc'=>58
        );
        $params['sign'] = $this->getReqSign($params,$app_key);
        return $this->doHttpPost($this->url, $params);
    }

    public function formatVoice($response)
    {
        $response_format = json_decode($response,true);
        return isset($response_format['data']['speech']) ? $response_format['data']['speech'] : '';
    }

    public function saveVoice($response,$path,$file_name){
        $speech = $this->formatVoice($response);
        file_put_contents($path.$file_name.'.mp3', base64_decode($speech));
    }

    protected function getReqSign($params,$app_key){
        // 0. 补全基本参数
        // 1. 字典升序排序
        ksort($params);

        // 2. 拼按URL键值对
        $str = '';
        foreach ($params as $key => $value)
        {
            if ($value !== '')
            {
                $str .= $key . '=' . urlencode($value) . '&';
            }
        }

        // 3. 拼接app_key
        $str .= 'app_key=' . $app_key;

        // 4. MD5运算+转换大写，得到请求签名
        $sign = strtoupper(md5($str));
        return $sign;
    }


// doHttpPost ：执行POST请求，并取回响应结果
// 参数说明
//   - $url   ：接口请求地址
//   - $params：完整接口请求参数（特别注意：不同的接口，参数对一般不一样，请以具体接口要求为准）
// 返回数据
//   - 返回false表示失败，否则表示API成功返回的HTTP BODY部分
    protected function doHttpPost($url, $params)
    {
        $curl = curl_init();

        $response = false;
        do
        {
            // 1. 设置HTTP URL (API地址)
            curl_setopt($curl, CURLOPT_URL, $url);

            // 2. 设置HTTP HEADER (表单POST)
            $head = array(
                'Content-Type: application/x-www-form-urlencoded'
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $head);

            // 3. 设置HTTP BODY (URL键值对)
            $body = http_build_query($params);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            // 4. 调用API，获取响应结果
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            $_http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($_http_code != 200)
            {
                $msg = curl_error($curl);
                $response = json_encode(array('ret' => -1, 'msg' => "sdk http post err: {$msg}", 'http_code' => $_http_code));
                break;
            }
        } while (0);

        curl_close($curl);
        return $response;
    }
}