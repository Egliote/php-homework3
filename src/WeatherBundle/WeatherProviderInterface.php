<?php

namespace Nfq\WeatherBundle;

interface WeatherProviderInterface
{
    /**
     * Get current Weather for given location
     *
     * @param Location $location
     *
     * @return Weather
     *
     * @throws WeatherProviderException
     */
    public function fetch(Location $location): Weather;
}
