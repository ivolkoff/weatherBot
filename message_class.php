<?php
include "telegramBot.php";

$bot = new bottest\TelegramBot();


$bot->addHandler(function($update, &$bot){
    if(explode(' ', $update->{"message"}->{"text"})[0] === '/start')

        $bot->sendMessage($update->{"message"}->{"chat"}->{"id"}, 'Привет, ' . $update->{"message"}->{"chat"}->{"first_name"} . '!');
});

// Опрос серверов с ожиданием в 5 секунд
$bot->poll(5);

