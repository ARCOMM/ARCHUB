<?php

namespace App;

use Exception;

enum ChannelEnum
{
    case ARCHUB;
    case STAFF;

    public function id(): string
    {
        return match ($this) {
            ChannelEnum::ARCHUB => config('services.discord.archub_webhook'),
            ChannelEnum::STAFF => config('services.discord.staff_webhook'),
            default => throw new Exception("Webhook not found"),
        };
    }
}
