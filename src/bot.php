<?php

namespace Bot;

include __DIR__.'/../vendor/autoload.php';

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\MessageReaction;
use Discord\Parts\WebSockets\PreenceUpdate;
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
        $channel = $message->channel;
        $appMessage = $application->messageHandler->createMessage($message->author->username, $message->author->id, $message->content, $message->id, $message->timestamp, $channel->id, ($discord->user->id === $message->author->id));
        $application->historyListHandler->addMessageToHistory($application->historyList, $appMessage);

        sleep(rand(0, 2));

        if (!$application->botHandler->shouldIAnswer($discord->user->id, $appMessage)) {
            return;
        }

        $channel->broadcastTyping()->then(function () use ($application, $channel, $appMessage) {
            $channelList = $application->historyList->getChannelList($channel->id);
            $conversationHistory = $application->promptHandler->generatePromptFromHistory($channelList);

            if ($application->messageHandler->hasImage($appMessage) 
            && $imageUrl = $application->messageHandler->extractImage($appMessage)) {

                $imageDescription = $application->openAIVisionClient->getDescription($imageUrl);

                if (!empty($imageDescription)) {
                    $imageDescriptionMessage = $application->messageHandler->createMessage($appMessage->author, $appMessage->id.rand(), "(sent a picture described as: ".$imageDescription.")", uniqid(), (new \DateTime())->format('Y-m-d H:i:s'), $channel->id, true);
                    $application->historyListHandler->addMessageToHistory($application->historyList, $imageDescriptionMessage);
                }
            }

            $completion = $application->openAIClient->getCompletion($conversationHistory);

            if (empty($completion)) {
                return;
            }
            if ($completion !== end($channelList)->message) {
                $channel->sendMessage($completion);
            }
        });
    });

    $discord->on(Event::MESSAGE_REACTION_ADD, function (MessageReaction $messageReaction, Discord $discord) use ($application) {
        if ($application->configuration->getConfig()['openai']['rewardEmoji'] !== $messageReaction->emoji->name) {
            return;
        }

        $channel = $messageReaction->channel;
        $channelList = $application->historyList->getChannelList($channel->id);
        $application->promptHandler->extractPromptFileFromChannelList($channelList, $messageReaction->message_id);
    });
});

$discord->run();
