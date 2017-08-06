<?php

namespace RunetId\ApiClient\Iterator;

use Ruvents\AbstractApiClient\ApiClientInterface;

class PageTokenIterator implements IteratorInterface, \Countable
{
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

    public function setContext(array $context)
    {
        $this->context = $context;
        $this->nextMaxResults = isset($context['query']['MaxResults']) ? $context['query']['MaxResults'] : null;
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
        if (!isset($this->data[$this->index])) {
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

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->loaded ? $this->data : iterator_to_array($this);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->loaded ? count($this->data) : iterator_count($this);
    }

    protected function loadData()
    {
        if ($this->loaded) {
            return;
        }

        /** @var ApiClientInterface $apiClient */
        $apiClient = $this->context['api_client'];

        // copy and modify context
        $context = $this->context;
        $context['iterator'] = false;
        $context['query']['MaxResults'] = $this->nextMaxResults;
        $context['query']['PageToken'] = $this->nextPageToken;

        // request raw data
        $rawData = $apiClient->request($context);

        // extract data
        $data = $rawData[$context['data_path']];

        $countData = count($data);

        $this->data = array_merge($this->data, array_values($data));

        if (null !== $this->nextMaxResults) {
            $this->nextMaxResults -= $countData;
        }

        if (0 === $countData || 0 === $this->nextMaxResults || !isset($rawData['NextPageToken'])) {
            $this->loaded = true;
        } else {
            $this->nextPageToken = $rawData['NextPageToken'];
        }
    }
}
