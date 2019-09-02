<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductSetPageSearch\Plugin\Elasticsearch\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\ElasticsearchSearchContextTransfer;
use Generated\Shared\Transfer\ProductSetDataStorageTransfer;
use Generated\Shared\Transfer\SearchContextTransfer;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\SearchExtension\Dependency\Plugin\QueryInterface;
use Spryker\Client\SearchExtension\Dependency\Plugin\SearchContextAwareQueryInterface;
use Spryker\Shared\ProductSetPageSearch\ProductSetPageSearchConstants;

class ProductSetPageSearchListQueryPlugin extends AbstractPlugin implements QueryInterface, SearchContextAwareQueryInterface
{
    protected const SOURCE_NAME = 'page';

    /**
     * @var int|null
     */
    protected $limit;

    /**
     * @var int|null
     */
    protected $offset;

    /**
     * @var \Elastica\Query
     */
    protected $query;

    /**
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct($limit = null, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        $this->query = $this->createSearchQuery();
    }

    /**
     * {@inheritdoc}
     * - Returns a query object for product set page search.
     *
     * @api
     *
     * @return \Elastica\Query
     */
    public function getSearchQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     * - Defines a context for product set page search.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\SearchContextTransfer
     */
    public function getSearchContext(): SearchContextTransfer
    {
        $searchContextTransfer = new SearchContextTransfer();
        $searchContextTransfer = $this->expandWithVendorContext($searchContextTransfer);

        return $searchContextTransfer;
    }

    /**
     * @return \Elastica\Query
     */
    protected function createSearchQuery()
    {
        $query = new Query();

        $this->setQuery($query)
            ->setSorting($query)
            ->setLimit($query)
            ->setOffset($query)
            ->setSource($query);

        return $query;
    }

    /**
     * @param \Elastica\Query $baseQuery
     *
     * @return $this
     */
    protected function setQuery(Query $baseQuery)
    {
        $boolQuery = new BoolQuery();
        $this->setTypeFilter($boolQuery);

        $baseQuery->setQuery($boolQuery);

        return $this;
    }

    /**
     * @param \Elastica\Query\BoolQuery $boolQuery
     *
     * @return void
     */
    protected function setTypeFilter(BoolQuery $boolQuery)
    {
        $typeFilter = new Match();
        $typeFilter->setField(PageIndexMap::TYPE, ProductSetPageSearchConstants::PRODUCT_SET_RESOURCE_NAME);

        $boolQuery->addMust($typeFilter);
    }

    /**
     * @param \Elastica\Query $query
     *
     * @return $this
     */
    protected function setSorting(Query $query)
    {
        $query->addSort(
            [
                PageIndexMap::INTEGER_SORT . '.' . ProductSetDataStorageTransfer::WEIGHT => [
                    'order' => 'desc',
                    'mode' => 'min',
                    'unmapped_type' => 'integer',
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Elastica\Query $query
     *
     * @return $this
     */
    protected function setLimit(Query $query)
    {
        if ($this->limit) {
            $query->setSize($this->limit);
        }

        return $this;
    }

    /**
     * @param \Elastica\Query $query
     *
     * @return $this
     */
    protected function setOffset(Query $query)
    {
        if ($this->offset) {
            $query->setFrom($this->offset);
        }

        return $this;
    }

    /**
     * @param \Elastica\Query $query
     *
     * @return $this
     */
    protected function setSource(Query $query)
    {
        $query->setSource([PageIndexMap::SEARCH_RESULT_DATA]);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\SearchContextTransfer $searchContextTransfer
     *
     * @return \Generated\Shared\Transfer\SearchContextTransfer
     */
    protected function expandWithVendorContext(SearchContextTransfer $searchContextTransfer): SearchContextTransfer
    {
        $elasticsearchSearchContextTransfer = new ElasticsearchSearchContextTransfer();
        $elasticsearchSearchContextTransfer->setSourceName(static::SOURCE_NAME);
        $searchContextTransfer->setElasticsearchContext($elasticsearchSearchContextTransfer);

        return $searchContextTransfer;
    }
}
