<?php

require_once(getcwd() . "/../vendor/autoload.php");

use Dropsolid\UnomiSdkPhp\Http\Guzzle\GuzzleApiClientFactory;
use Dropsolid\UnomiSdkPhp\Unomi;

$apiClient = GuzzleApiClientFactory::createBasicAuth(
    [
        'timeout' => 3.0,
        'base_uri' => 'localhost',
        'auth' => ['karaf', 'karaf']
    ]
);

$unomi = Unomi::withDefaultSerializer($apiClient);

// Performing a segment.list request with offset
$offset = [
    'offset' => 0,
];

// Get all itemIds & conditions from all existing segments
try {
    $segments = $unomi->segments()->listSegments($offset);
    foreach ($segments as $segmentMetadata) {
        $id = $segmentMetadata->getId();
        // Performing a segment.info request
        $segment = $unomi->segments()->getSegment($id);
        var_dump($segment->getItemId());
        var_dump($segment->getCondition());
    }
} catch (\Http\Client\Exception $e) {
    // Handle exception here.
}
