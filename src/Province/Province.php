<?php

namespace Vietiso\OneGuide\Province;

use Vietiso\OneGuide\Client;
use Vietiso\OneGuide\Paginator\Collection;

class Province
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function list(?string $countryCode = null, int $limit = 10, ?string $nextCursor = null): Collection
    {
        $endpoint = "provinces";
        $params = [
            'country_code' => $countryCode,
            'limit' => $limit,
            'next_cursor' => $nextCursor
        ];
        $response = $this->client->send($endpoint, $params);
        return new Collection($this->client, $response, $endpoint, $params);
    }
}