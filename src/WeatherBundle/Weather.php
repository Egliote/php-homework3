<?php

namespace Nfq\WeatherBundle;

class Weather
{
    /**
     * @var float
     */
    private $temperature;

    public function __construct(float $temperature)
    {
        $this->temperature = $temperature;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature)
    {
        $this->temperature = $temperature;
    }
}
