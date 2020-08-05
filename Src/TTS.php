<?php
namespace Src;

class TTS{
    private $type_func = '';
    private $app_id = '';
    private $app_key = '';
    private $text = '';
    private $instance = '';
    private $res = '';
    /**
     * TTS constructor.
     * @param $type Tencent,
     * @param string $app_id
     * @param string $app_key
     * @param $text
     */
    public function __construct($type,$app_id='',$app_key='',$text)
    {
        $this->type_func = 'Src\Plug\\'.$type.'TTS';
        $this->app_id = $app_id;
        $this->app_key = $app_key;
        $this->text = $text;
        $this->instance = ($this->type_func)::getInstance();
        return $this;
    }

    public function getVoice(){
        $this->res = $this->instance->init($this->app_id,$this->app_key,$this->text);
        return $this;
    }

    public function format(){
        return $this->instance->formatVoice($this->res);
    }

    public function save($path,$file_name=''){
        $file_name == '' ? $file_name=time():'';
        $this->instance->saveVoice($this->res,$path,$file_name);
        return $path.$file_name.'.mp3';
    }

}