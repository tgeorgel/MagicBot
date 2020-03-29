<?php

namespace App\Traits;

trait BotChannelsTrait
{
    public function channelExistsName(string $name): bool
    {
        try {
            $this->query->channelGetByName($name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
