<?php

namespace LaravelGoogleCalendar\Infra\Adapters;

use Google_Service_Calendar;
use LaravelGoogleCalendar\Domain\Contracts\GoogleCalendarContract;

class GoogleCalendar implements GoogleCalendarContract
{
    public function __construct(
        private readonly Google_Service_Calendar $googleServiceCalendar,
    ) {
    }
}
