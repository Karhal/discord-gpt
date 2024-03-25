<?php

namespace Bot;

include __DIR__.'/../vendor/autoload.php';

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;

$application = new Application();

$discord = new Discord([
    'token' => $application->configuration->getConfig()['discord']['token'],
    'intents' => Intents::getDefaultIntents(),
]);

$discord->on('ready', function (Discord $discord) use ($application) {
    echo 'Bot is ready!', PHP_EOL;
    $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($application) {
        handleNewMessage($message, $discord, $application);
    });
});

function handleNewMessage(Message $message, Discord $discord, Application $application)
{
    $channel = $message->channel;
    $appMessage = $application->messageHandler->createMessage($message->author->username, $message->author->id, $message->content, $message->id, $message->timestamp, $channel->id, ($discord->user->id === $message->author->id));
    
    if (!$application->botHandler->shouldIAnswer($discord->user->id, $appMessage)) {
        return;
    }

    $application->historyListHandler->addMessageToHistory($application->historyList, $appMessage);

    sleep(rand(0, 2));

    $channel->broadcastTyping()->then(function () use ($application, $channel, $appMessage) {
        $application->messageHandler->sendMessage($application, $channel, $appMessage);
    });
}

