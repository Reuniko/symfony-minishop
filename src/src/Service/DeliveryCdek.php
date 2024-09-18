<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeliveryCdek
{
    public function __construct(
        private string              $apiEndpoint,
        private string              $username,
        private string              $password,
        private HttpClientInterface $httpClient,
        private CacheInterface      $cache,
    )
    {
    }

    public function getPrice(string $weight)
    {
        $headers = ['Content-Type' => 'application/json'];
        $requestData = [
            'username' => $this->username,
            'password' => $this->password,
            'weight' => $weight,
        ];

        $answer = [
            'price' => '???',
            'minDays' => '???',
            'maxDays' => '???',
            'error' => '',
        ];

        try {
            $cacheKey = sprintf('delivery_info_%s_%s', 'cdek', $weight);
            $responseData = $this->cache->get($cacheKey, function (ItemInterface $item) use ($headers, $requestData) {
                $item->expiresAfter(3600);
                $response = $this->httpClient->request(
                    'POST',
                    $this->apiEndpoint,
                    [
                        'headers' => $headers,
                        'body' => json_encode($requestData),
                        'timeout' => 1,
                    ]
                );
                return json_decode($response->getContent(), true);
            });
            if ($responseData['status'] === true || $responseData['status'] === 200) {
                $answer['price'] = $responseData['data']['price'];
                $answer['minDays'] = $responseData['data']['delivery_min_days'];
                $answer['maxDays'] = $responseData['data']['delivery_max_days'];
            } else {
                throw new \Exception("Статус ответа - {$responseData['status']}");
            }
        } catch (\Exception $error) {
            $answer['error'] = 'Не удалось получить данные о доставке от CDEK: ' . $error->getMessage();
        }
        return $answer;
    }
}