<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiCityService {

        private $client;
      //  private $cityInput = getCityInput();

        public function __construct(HttpClientInterface $client) {
            $this->client = $client;
    }


    public function getCities(): array
    {
        $response = $this->client->request(
            'GET',
            'https://geo.api.gouv.fr/communes?&boost=population&limit=5'
        );

        return $response->toArray(); //TODO the twig to go


    }
}