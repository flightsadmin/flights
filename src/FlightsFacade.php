<?php

namespace Flightsadmin\Flights;

use Illuminate\Support\Facades\Facade;

class FlightsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'livewire-crud';
    }
}
