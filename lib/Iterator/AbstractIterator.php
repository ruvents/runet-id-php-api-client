<?php

namespace RunetId\ApiClient\Iterator;

use Ruvents\AbstractApiClient\ApiClientInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class AbstractIterator implements \Iterator
{
    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;

    /**
     * @var ApiClientInterface
     */
    private $apiClient;

    /**
     * @var array
     */
    private $context;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var null|string
     */
    private $nextPageToken;

    /**
     * @var null|int
     */
    private $nextMaxResults;

    /**
     * @var bool
     */
    private $loaded = false;

    public function __construct(ApiClientInterface $apiClient, DenormalizerInterface $denormalizer, array $context)
    {
        $this->apiClient = $apiClient;
        $this->denormalizer = $denormalizer;
        $this->context = $context;

        $maxResultsParName = $this->getMaxResultsParameterName();
        $this->nextMaxResults = isset($this->context['query'][$maxResultsParName])
            ? $this->context['query'][$maxResultsParName] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->data[$this->index];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if (!$this->loaded && !isset($this->data[$this->index])) {
            $this->loadData();
        }

        return isset($this->data[$this->index]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }

    protected function loadData()
    {
        $maxResultsParName = $this->getMaxResultsParameterName();
        $pageTokenParName = $this->getPageTokenParameterName();
        $nextPageTokenParName = $this->getNextPageTokenParameterName();

        $context = $this->context;
        $context['query'][$pageTokenParName] = $this->nextPageToken;
        $context['query'][$maxResultsParName] = $this->nextMaxResults;
        $context['denormalize'] = false;

        $rawData = $this->apiClient->request($context);

        $extractedData = $this->denormalize($rawData);
        $countExtractedData = count($extractedData);

        $this->data = array_merge($this->data, $extractedData);

        if (null !== $this->nextMaxResults) {
            $this->nextMaxResults -= $countExtractedData;
        }

        if (0 === $countExtractedData || 0 === $this->nextMaxResults || !isset($rawData[$nextPageTokenParName])) {
            $this->loaded = true;
        } else {
            $this->nextPageToken = $rawData[$nextPageTokenParName];
        }
    }

    /**
     * @return string
     */
    protected function getMaxResultsParameterName()
    {
        return 'MaxResults';
    }

    /**
     * @return string
     */
    protected function getPageTokenParameterName()
    {
        return 'PageToken';
    }

    /**
     * @return string
     */
    protected function getNextPageTokenParameterName()
    {
        return 'NextPageToken';
    }

    /**
     * @param array $rawData
     *
     * @return array
     */
    abstract protected function denormalize(array $rawData);
}