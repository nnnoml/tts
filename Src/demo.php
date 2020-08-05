<?php

require '../vendor/autoload.php';

//请在此填入AppID与AppKey
$app_id = '215***9476';
$app_key = 'hS****0c7Z849';

$text = '123';


$TTS = new \Charis\TTS\TTS('Tencent',$app_id,$app_key,$text);
$tts_format = $TTS->getVoice()->format();
var_dump($tts_format);
$tts_save = $TTS->getVoice()->save('./','tencent');
var_dump($tts_save);


$TTS = new \Charis\TTS\TTS('Bugscaner',$app_id,$app_key,$text);
$tts_format = $TTS->getVoice()->format();
var_dump($tts_format);
$tts_save = $TTS->getVoice()->save('./','bug');
var_dump($tts_save);