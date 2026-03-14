<?php

namespace App\Http\Client;

use Stripe\HttpClient\ClientInterface;
use Stripe\Exception\ApiConnectionException;

/**
 * Client HTTP pour Stripe utilisant les stream wrappers PHP natifs
 * au lieu de curl (bloqué sur le port 443 par OVH mutualisé).
 */
class StripeStreamClient implements ClientInterface
{
    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
    {
        $method = strtoupper($method);

        $httpHeaders = [];
        foreach ($headers as $header) {
            $httpHeaders[] = $header;
        }

        $body = null;
        if ($method === 'POST') {
            $body = http_build_query($params, '', '&');
            $httpHeaders[] = 'Content-Type: application/x-www-form-urlencoded';
        } elseif ($method === 'GET' && ! empty($params)) {
            $absUrl .= '?' . http_build_query($params, '', '&');
        } elseif ($method === 'DELETE' && ! empty($params)) {
            $absUrl .= '?' . http_build_query($params, '', '&');
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $httpHeaders),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 80,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $response = @file_get_contents($absUrl, false, $context);

        if ($response === false) {
            throw new ApiConnectionException(
                "Impossible de se connecter à Stripe ({$absUrl}). Vérifiez votre connexion internet."
            );
        }

        $statusCode = 200;
        $responseHeaders = [];
        if (isset($http_response_header)) {
            foreach ($http_response_header as $i => $header) {
                if ($i === 0 && preg_match('/HTTP\/\S+\s+(\d+)/', $header, $matches)) {
                    $statusCode = (int) $matches[1];
                } else {
                    $parts = explode(':', $header, 2);
                    if (count($parts) === 2) {
                        $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                    }
                }
            }
        }

        return [$response, $statusCode, $responseHeaders];
    }
}
