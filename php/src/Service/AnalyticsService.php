<?php

namespace App\Service;

use App\Entity\ISerializable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AnalyticsService
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        protected string $analyticsToken,
        protected string $analyticsHost
    ) {
        $headers = [
            'Authorization' => 'Bearer ' . $this->analyticsToken,
            'Content-Type' => 'application/json',
        ];

        $this->httpClient = $httpClient->withOptions([
            'base_uri' => $analyticsHost,
            'headers' => $headers,
        ]);
    }

    abstract function send(ISerializable $data): void;
}