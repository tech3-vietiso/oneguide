<?php

namespace Vietiso\OneGuide\Paginator;

use Vietiso\OneGuide\Client;
use IteratorAggregate;
use Traversable;

class Collection implements IteratorAggregate
{
    private Client $client;

    private array $response;
    
    private string $endpoint;

    private array $params;

    public function __construct(
        Client $client,
        array $response,
        string $endpoint,
        array $params
    )
    {
        $this->client = $client;
        $this->response = $response;
        $this->endpoint = $endpoint;
        $this->params = $params;
    }

    public function hasMore(): bool
    {
        return !empty($this->response['next_cursor']);
    }

    public function getNextCursor(): ?string
    {
        return $this->hasMore() ? $this->response['next_cursor'] : null;
    }
    
    private function getNextPage(): void
    {
        $params = $this->params;
        $params['next_cursor'] = $this->getNextCursor();
        $this->response = $this->client->send($this->endpoint, $params);
    }

    public function getItems(): array
    {
        return $this->response['data'];
    }

    public function getIterator(): Traversable
    {
        while (true) {
            foreach ($this->getItems() as $item) {
                yield $item;
            }

            if ($this->hasMore()) {
                $this->getNextPage();
                continue;
            }

            break;
        }
    }
}