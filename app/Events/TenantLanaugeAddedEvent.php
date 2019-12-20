<?php

namespace App\Events;

class TenantLanaugeAddedEvent extends Event
{
    /**
     * @var string
     */
    public $tenantName;

    /**
     * @var string
     */
    public $languageCode;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $tenantName, string $languageCode)
    {
        $this->tenantName = $tenantName;
        $this->languageCode = $languageCode;
    }
}
