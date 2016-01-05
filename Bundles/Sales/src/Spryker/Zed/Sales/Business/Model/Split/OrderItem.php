<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Sales\Business\Model\Split;

use Generated\Shared\Transfer\ItemSplitResponseTransfer;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Spryker\Zed\Sales\Business\Model\Split\Validation\ValidatorInterface;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Orm\Zed\Sales\Persistence\SpySalesOrderItemOption;
use Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface;

class OrderItem implements ItemInterface
{

    const SPLIT_MARKER = 'split#';

    /**
     * @var ConnectionInterface
     */
    protected $databaseConnection;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SalesQueryContainerInterface
     */
    protected $salesQueryContainer;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @param ValidatorInterface $validator
     * @param SalesQueryContainerInterface $salesQueryContainer
     * @param CalculatorInterface $splitCalculator
     */
    public function __construct(
        ValidatorInterface $validator,
        SalesQueryContainerInterface $salesQueryContainer,
        CalculatorInterface $splitCalculator
    ) {
        $this->validator = $validator;
        $this->salesQueryContainer = $salesQueryContainer;
        $this->calculator = $splitCalculator;
    }

    /**
     * @param int $idSalesOrderItem
     * @param int $quantityToSplit
     *
     * @throws \Exception
     *
     * @return ItemSplitResponseTransfer
     */
    public function split($idSalesOrderItem, $quantityToSplit)
    {
        $salesOrderItem = $this->salesQueryContainer
            ->querySalesOrderItem()
            ->findOneByIdSalesOrderItem($idSalesOrderItem);

        $splitResponse = new ItemSplitResponseTransfer();
        if ($this->validator->isValid($salesOrderItem, $quantityToSplit) === false) {
            return $splitResponse
                ->setSuccess(false)
                ->setValidationMessages($this->validator->getMessages());
        }

        try {
            $this->getConnection()->beginTransaction();
            $newSalesOrderItem = $this->copy($salesOrderItem, $quantityToSplit);
            $this->updateParentQuantity($salesOrderItem, $quantityToSplit);

            $this->getConnection()->commit();

            return $splitResponse
                ->setSuccess(true)
                ->setIdOrderItem($newSalesOrderItem->getIdSalesOrderItem())
                ->setSuccessMessage(
                    sprintf(Validation\Messages::SPLIT_SUCCESS_MESSAGE, $salesOrderItem->getIdSalesOrderItem())
                );
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * @return ConnectionInterface
     */
    protected function getConnection()
    {
        if ($this->databaseConnection === null) {
            $this->databaseConnection = Propel::getConnection();
        }

        return $this->databaseConnection;
    }

    /**
     * @param ConnectionInterface $databaseConnection
     *
     * @return void
     */
    public function setDatabaseConnection(ConnectionInterface $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * @param SpySalesOrderItem $salesOrderItem
     * @param int $quantity
     *
     * @return SpySalesOrderItem;
     */
    protected function copy(SpySalesOrderItem $salesOrderItem, $quantity)
    {
        $copyOfSalesOrderItem = $this->createSalesOrderItemCopy($salesOrderItem, $quantity);

        foreach ($salesOrderItem->getOptions() as $salesOrderItemOption) {
            $this->createOrderItemOptionCopy($salesOrderItemOption, $copyOfSalesOrderItem);
        }

        return $copyOfSalesOrderItem;
    }

    /**
     * @param SpySalesOrderItem $salesOrderItem
     * @param int $quantity
     *
     * @return SpySalesOrderItem
     */
    protected function createSalesOrderItemCopy(SpySalesOrderItem $salesOrderItem, $quantity)
    {
        $copyOfSalesOrderItem = $salesOrderItem->copy(false);

        $copyOfSalesOrderItem->setGroupKey(self::SPLIT_MARKER . $copyOfSalesOrderItem->getGroupKey());
        $copyOfSalesOrderItem->setCreatedAt(new \DateTime());
        $copyOfSalesOrderItem->setQuantity($quantity);
        $copyOfSalesOrderItem->setLastStateChange(new \DateTime());
        $copyOfSalesOrderItem->save($this->getConnection());

        return $copyOfSalesOrderItem;
    }

    /**
     * @param SpySalesOrderItemOption $salesOrderItemOption
     * @param SpySalesOrderItem $copyOfSalesOrderItem
     *
     * @return SpySalesOrderItemOption
     */
    protected function createOrderItemOptionCopy(
        SpySalesOrderItemOption $salesOrderItemOption,
        SpySalesOrderItem $copyOfSalesOrderItem
    ) {
        $copyOfOrderItemOption = $salesOrderItemOption->copy(false);

        $copyOfOrderItemOption->setCreatedAt(new \DateTime());
        $copyOfOrderItemOption->setFkSalesOrderItem($copyOfSalesOrderItem->getIdSalesOrderItem());
        $copyOfOrderItemOption->save($this->getConnection());

        return $copyOfOrderItemOption;
    }

    /**
     * @param SpySalesOrderItem $salesOrderItem
     * @param int $quantity
     *
     * @return void
     */
    protected function updateParentQuantity(SpySalesOrderItem $salesOrderItem, $quantity)
    {
        $quantityAmountLeft = $this->calculator->calculateQuantityAmountLeft($salesOrderItem, $quantity);

        $salesOrderItem->setQuantity($quantityAmountLeft);
        $salesOrderItem->save($this->getConnection());
    }

}