<?php

namespace Makeable\DatabaseNotifications\Channels;

use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackAttachmentField;
use Illuminate\Notifications\Messages\SlackMessage;
use Makeable\DatabaseNotifications\Events\SlackNotificationSent;

class Slack extends Channel
{
    use ProxiesNotifications;

    /**
     * @param $data
     * @return mixed
     */
    public function deserialize($data)
    {
        return tap($this->buildObject(new SlackMessage, $data), function ($message) {
            $message->attachments = collect($message->attachments)->map(function ($attachment) {
                return tap($this->buildObject(new SlackAttachment, $attachment), function ($attachment) {
                    $attachment->fields = collect($attachment->fields)->map(function ($field) {
                        return $this->buildObject(new SlackAttachmentField, $field);
                    })->toArray();
                });
            })->toArray();
        });
    }

    /**
     * @return string
     */
    public function notificationSentEvent()
    {
        return SlackNotificationSent::class;
    }
}