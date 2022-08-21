<?php

use GuzzleHttp\Client;

function getHTML($url, $proxy = false)
{
    if ($proxy) {
        $requestUrl = PROXY_URL . $url;
    } else {
        $requestUrl = $url;
    }

    $httpClient = new Client();

    $response = $httpClient->get($requestUrl, [
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36',
        ],
    ]);

    $htmlString = (string) $response->getBody();

    // add this line to suppress any warnings
    libxml_use_internal_errors(true);

    $doc = new DOMDocument();
    $doc->loadHTML($htmlString);

    return $doc;
}
