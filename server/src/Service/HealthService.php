<?php

namespace App\Service;

class HealthService
{
    private string $appEnv;

    public function __construct($appEnv)
    {
        $this->appEnv = $appEnv;
    }

    public function getAppEnv(): string
    {
        return $this->appEnv;
    }
}
