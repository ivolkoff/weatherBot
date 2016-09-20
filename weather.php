<?php


namespace bottest;

include_once 'key.php';

class Weather extends Key
{   public $text="кострома";

    public function apiWeather($fields= array())
    {
        $url = "http://api.openweathermap.org/data/2.5/weather?q=".$this->text."&appid=".$this->weatherKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if($response === false)
            $this->processRequestError(curl_error($ch));
        curl_close($ch);
        $response = json_decode($response);

        return($response);
        //die('$response = '. print_r($response, true)."\n");





   //  print_r($response['name']['main.temp']['wind.speed']);



    }


    private function processRequestError($error)
    {
        $this->processError("Ошибка" . $error);
    }


public function getWeater()
   {

       $getText= array(

           'name'=> $this->apiWeather('name'),
           'temp'=>$this->apiWeather('main.temp')
       );

   }



}