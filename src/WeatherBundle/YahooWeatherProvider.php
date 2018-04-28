<?php
/**
 * Created by PhpStorm.
 * User: egliote
 * Date: 18.4.6
 * Time: 14.43
 */

namespace Nfq\WeatherBundle;

use Nfq\WeatherBundle\Location;
use Nfq\WeatherBundle\Weather;
use Nfq\WeatherBundle\WeatherProviderException;

class YahooWeatherProvider implements WeatherProviderInterface
{
    public function __construct()
    {
    }

    public function fetch(Location $location): Weather
    {
        return new Weather($this->fetchCurrentTemperature($location));
    }

    private function fetchCurrentTemperature(Location $location): float
    {
        $base_url = "http://query.yahooapis.com/v1/public/yql";
        $yql_query = 'select * from weather.forecast where woeid in (SELECT woeid FROM geo.places WHERE text='
            .'"(%d,%d)") and u="C"';
        $url = sprintf(
            $yql_query,
            $location->getLon(),
            $location->getLat()
        );
        $yql_query_url = $base_url."?q=".urlencode($url)
            ."&format=json";
        // Check for curl
        if (!function_exists('curl_version')) {
            throw new WeatherProviderException('curl is disabled. Install and/or enable it');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $yql_query_url);
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
            throw new WeatherProviderException('Can\'t get JSON from yahoo');
        }

        return $this->getTemperatureValue($json);
    }

    private function getTemperatureValue($json): float
    {
        if (isset($json->query->results) && (null === $json->query->results)) {

            throw new WeatherProviderException('Yahoo didn\'t returned required data');
        }
        // Get temperature from json
        if (isset($json->query->results->channel->item->condition->temp)) {
            $temperature = $json->query->results->channel->item->condition->temp;
        } else {
            throw new WeatherProviderException('Can\'t get temperature from yahoo');
        }
        return $temperature;
    }
}