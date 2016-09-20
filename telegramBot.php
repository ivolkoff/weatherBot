<?php


namespace bottest;

include_once 'key.php';


class TelegramBot extends Key
{

    private $handlers= array();
    private $updateId = 0;


   // public function __construct($token)
   // {



   // }


    public function requestApi($metod, $fields= array())
    {
        $url = "https://api.telegram.org/bot". $this->token . "/" .$metod;

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
        if(!$response->{"ok"})
            $this->processAPIError($response);
        return $response;
    }


    public function sendMessage($chat_id, $text)
    {
        $this->requestApi('sendMessage', array(
            'chat_id' => $chat_id,
            'text' => $text
        ));
    }

    private function processRequestError($error)
    {
        $this->processError("Ошибка" . $error);
    }



    private function processAPIError($response)
    {
        $this->processError("Ошибка при работе с API. Детали: " . json_encode($response));
    }


    private function processError($error)
    {
        // Уведомляем сервер о последнем успешно обработанном обновлении
        $this->requestApi('getUpdates', array('offset' => $this->updateId + 1));
        // Если указан id чата администратора, то уведомить его
        if(defined('TELEGRAM_ADMIN_CHATID'))
            $this->sendMessage(TELEGRAM_ADMIN_CHATID, $error);
        die($error);
    }




    public function getUpdates(){
        return $this->requestApi('getUpdates', array('offset' => $this->updateId + 1));
    }


    public function addHandler($handler){
        $this->handlers[] = $handler;
    }


    private function processUpdate($update){

        $this->updateId = $update->{"update_id"};
        for($j = 0; $j < count($this->handlers); $j++)

            if($this->handlers[$j]($update, $this) === false)
                break;
    }



    public function poll($timeout){
        while(true) {
            $updates = $this->getUpdates();
            // Передать обновления на обработку
            for($i = 0; $i < count($updates->{"result"}); $i++)
                $this->processUpdate($updates->{"result"}[$i]);
            sleep($timeout);
        }
    }






}