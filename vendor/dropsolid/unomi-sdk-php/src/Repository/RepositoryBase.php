<?php

namespace Dropsolid\UnomiSdkPhp\Repository;

use Dropsolid\UnomiSdkPhp\Http\ApiClient\ApiClientInterface;
use Dropsolid\UnomiSdkPhp\Request\RequestInterface;
use Http\Client\Exception;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class RepositoryBase
 *
 * @package Dropsolid\UnomiSdkPhp\Repository
 */
abstract class RepositoryBase
{
    /**
     * @var ApiClientInterface
     */
    protected $apiClient;

    /**
     * @var SerializerInterface&NormalizerInterface
     */
    protected $serializer;

    /**
     * RepositoryBase constructor.
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
     * @param $data
     * @param array $context
     * @return string|array
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     *
     * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer
     */
    protected function normalize($data, $context = array())
    {
        return $this->serializer->normalize($data, 'json', $context);
    }

    /**
     * @param RequestInterface $request
     * @param $responseClass
     *
     * @return mixed
     * @throws Exception
     */
    protected function handleRequest(RequestInterface $request, $responseClass)
    {
        $response = $this->apiClient->handle($request);
        $responseBody = $response->getBody()->getContents();
        return $this->deserialize($responseBody, $responseClass);
    }

    /**
     * @param $data
     * @param $type
     *
     * @return mixed
     */
    protected function deserialize($data, $type)
    {
        return $this->serializer->deserialize($data, $type, 'json');
    }
}
