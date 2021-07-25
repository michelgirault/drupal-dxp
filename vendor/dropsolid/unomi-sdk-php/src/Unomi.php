<?php

namespace Dropsolid\UnomiSdkPhp;

use Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClientInterface;
use Dropsolid\UnomiSdkPhp\Repository\SegmentRepository;
use Dropsolid\UnomiSdkPhp\Serializer\SerializerFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class Unomi
 *
 * @package Dropsolid\UnomiSdkPhp
 */
class Unomi
{
    /**
     * @var ApiClientInterface
     */
    private $apiClient;

    /**
     * @var SerializerInterface&NormalizerInterface
     */
    private $serializer;

    /**
     * Unomi constructor.
     *
     * @param ApiClientInterface $apiClient
     * @param SerializerInterface&NormalizerInterface $serializer
     */
    public function __construct(
        ApiClientInterface $apiClient,
        SerializerInterface $serializer
    ) {
    
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
    }

    /**
     * @param ApiClientInterface $apiClient
     *
     * @return Unomi
     */
    public static function withDefaultSerializer(ApiClientInterface $apiClient)
    {
        return new static($apiClient, SerializerFactory::create());
    }

    /**
     * @return SegmentRepository
     */
    public function segments()
    {
        return new SegmentRepository($this->apiClient, $this->serializer);
    }
}
