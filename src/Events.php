<?php

namespace App;

/**
 * Class Events.
 */
class Events
{
    /**
     * @Event("App\Event\EventsBatchEvent")
     */
    public const string EVENTS_BATCH = 'app.events.batch';
}
