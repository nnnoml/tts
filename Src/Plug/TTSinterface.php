<?php
namespace Charis\TTS\Plug;

interface TTSinterface{
    //初始化参数
    public function init($app_id,$app_key,$text);
    //格式化voice
    public function formatVoice($response);
    //保存voice
    public function saveVoice($response,$path,$file_name);
}