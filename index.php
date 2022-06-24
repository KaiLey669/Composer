<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


require_once __DIR__ . '/vendor/autoload.php';
$loader = new FilesystemLoader(__DIR__ . '/views');
$twig = new Environment($loader);

$log = new Logger('name');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/history.log',Logger::INFO));

echo $twig->render('index.html');


function addToHistory($login, $message){
    $messageJson = (object) ['user' => $login, 'message' => $message];
    $content = json_decode(file_get_contents("history.json"));
    $content->messages[] = $messageJson;
    file_put_contents("history.json", json_encode($content));
}

function printMessages(){
    $content = json_decode(file_get_contents("history.json"));
    foreach($content->messages as $message){
        echo "<p>";
        echo "$message->user: $message->message";
        echo "</p>";
    }
}

$adminLogin = "admin";
$adminPassword = "12345";

$login = $_GET["login"];
$password = $_GET["password"];
$message = $_GET["message"];    


if (($login === $adminLogin) && ($password === $adminPassword)){
    addToHistory($login, $message);
    $log->info('Send message', ['user' => $login, 'message' => $message]);
}
else{
    echo "Incorrect login or password";
    $log->error('Incorrect password');
}


printMessages();


