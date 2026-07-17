<?php

namespace Vietiso\OneGuide;

use Vietiso\OneGuide\Exception\ApiException;

class Client
{
    private string $apiKey;

    private string $secret;

    private string $url;

    public function __construct(array $params = [])
    {
        $this->apiKey = $params['api_key'];
        $this->secret = $params['secret'];
        $this->url = $params['url'];
    }

    public function send(string $endpoint, array $data)
    {
        $url = rtrim($this->url, '/') . '/' . ltrim($endpoint, '/');
        $body = json_encode($data);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-Api-Key: ' . $this->apiKey,
                'X-Api-Secret: ' . $this->secret,
            ],
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new ApiException("Request to \"{$url}\" failed: " . curl_error($ch));
        }

        $statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        $result = json_decode($response, true);
        $json = is_array($result) ? $result : null;

        if ($statusCode < 200 || $statusCode >= 300) {
            $message = $json['message'] ?? "Request to \"{$url}\" failed with status {$statusCode}.";
            $errors = $json['errors'] ?? [];

            if (!empty($errors)) {
                $message .= ' ' . json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            throw new ApiException($message, $statusCode, $errors, $json);
        }

        return $result;
    }
}
