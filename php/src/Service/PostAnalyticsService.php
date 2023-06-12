<?php

namespace App\Service;

use App\Entity\ISerializable;

final class PostAnalyticsService extends AnalyticsService
{
    public function send(ISerializable $data): void
    {
        $this->logger->debug('sending post analytics data: ' . $data->getSerialized());

        try {
            $response = $this->httpClient->request('POST', '/api/posts/', [
                'json' => $data->getValuesArray(),
            ]);

            $this->logger->debug('response: ' . $response->getContent());

        } catch (\Throwable $e) {
            $this->logger->error('Error during analytics event dispatch: ' . $e);
        }
    }
}