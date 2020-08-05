# tts
Integrate some tts  ，tencent and etc

#### how to use
```$xslt
//new class
$TTS = new \Src\TTS('Tencent',$app_id,$app_key,$text);
//format mp3 as base64
$TTS->getVoice()->format();
//save mp3
$TTS->getVoice()->save('./','bug');
```