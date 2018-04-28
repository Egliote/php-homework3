<?php

namespace Nfq\WeatherBundle;

use Nfq\WeatherBundle\Location;
use Nfq\WeatherBundle\Weather;
use Nfq\WeatherBundle\WeatherProviderException;

class OpenWeatherMapWeatherProvider implements WeatherProviderInterface
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Location $location): Weather
    {
        // TODO: Implement this
        //return new Weather(24.1);
        return new Weather($this->fetchCurrentTemperature($location));
    }

    private function fetchCurrentTemperature(Location $location):float
    {
        $url = sprintf(
            'http://api.openweathermap.org/data/2.5/weather?lat=%d&lon=%d&appid=%s&units=metric',
            $location->getLon(),
            $location->getLat(),
            $this->apiKey
        );
        if (!function_exists('curl_version')) {
            throw new WeatherProviderException('curl is disabled. Install and/or enable it');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $json_string = curl_exec($curl);
        // Check for curl errors
        if (curl_error($curl)) {
            $curl_err = curl_error($curl);
            curl_close($curl);
            throw new WeatherProviderException(sprintf('curl error: %s', $curl_err));
        }
        curl_close($curl);
        $json = json_decode($json_string);

        if (null === $json) {
            throw new WeatherProviderException('Can\'t get JSON from OpenWeatherMap');
        }

        return $this->getTemperatureValue($json);
    }

    public function getTemperatureValue($json): float
    {
        if (isset($json->cod)) {
            // Check status
            $json_code = $json->cod;
            if (200 !== $json_code) {
                if (isset($json->message)) {
                    // Display message text from openweathermap
                    $message = sprintf('Code: %d Message: %s', $json_code, $json->message);
                } else {
                    // Display code only with custom message
                    $message = sprintf('Code: %d Message: Unknown error occured', $json_code);
                }
                throw new WeatherProviderException($message);
            }
        }

        // Get temperature from json
        if (isset($json->main->temp)) {
            $temperature = $json->main->temp;
        } else {
            throw new WeatherProviderException('Can\'t get temperature from openweathermap');
        }
        return $temperature;
    }
}
