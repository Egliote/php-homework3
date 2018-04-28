<?php

namespace Nfq\WeatherBundle;

class Location
{
    /**
     * @var float
     */
    private $lat;

    /**
     * @var float
     */
    private $lon;

    public function __construct(float $lat, float $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLon(): float
    {
        return $this->lon;
    }
}
