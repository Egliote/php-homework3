<?php

namespace Nfq\WeatherBundle;

use Throwable;

class DelegatingWeatherProvider implements WeatherProviderInterface
{
    private $providers;
    /*@param WeatherProviderInterface[] $providers;*/

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function fetch(Location $location): Weather
    {
        foreach ($this->providers as $providerIterator)
        {
            try
            {
                $weather = $providerIterator->fetch($location);
                return $weather;
            }
            catch (Throwable $ex)
            {
                // This provider failed. Log error and let's try new one
                error_log(sprintf('Failed to get data. Message: %s', $ex->getMessage()));
            }
        }
        throw new WeatherProviderException('All providers failed to respond');
    }
}
