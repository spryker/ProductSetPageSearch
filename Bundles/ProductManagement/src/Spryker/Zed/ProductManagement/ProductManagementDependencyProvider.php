<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToCategoryBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToGlossaryBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToLocaleBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToMoneyBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToPriceBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToProductBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToProductImageBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToStockBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToTaxBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToTouchBridge;
use Spryker\Zed\ProductManagement\Dependency\Facade\ProductManagementToUrlBridge;
use Spryker\Zed\ProductManagement\Dependency\Service\ProductManagementToUtilTextBridge;

class ProductManagementDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_CATEGORY = 'FACADE_LOCALE';
    const FACADE_LOCALE = 'FACADE_LOCALE';
    const FACADE_PRODUCT = 'FACADE_PRODUCT';
    const FACADE_PRODUCT_IMAGE = 'FACADE_PRODUCT_IMAGE';
    const FACADE_TOUCH = 'FACADE_TOUCH';
    const FACADE_URL = 'FACADE_URL';
    const FACADE_TAX = 'FACADE_TAX';
    const FACADE_PRICE = 'FACADE_PRICE';
    const FACADE_GLOSSARY = 'FACADE_GLOSSARY';
    const FACADE_STOCK = 'FACADE_STOCK';
    const FACADE_MONEY = 'FACADE_MONEY';

    const SERVICE_UTIL_TEXT = 'SERVICE_UTIL_TEXT';

    const QUERY_CONTAINER_CATEGORY = 'QUERY_CONTAINER_CATEGORY';
    const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';
    const QUERY_CONTAINER_STOCK = 'QUERY_CONTAINER_STOCK';
    const QUERY_CONTAINER_PRODUCT_IMAGE = 'QUERY_CONTAINER_PRODUCT_IMAGE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::FACADE_PRODUCT] = function (Container $container) {
            return new ProductManagementToProductBridge($container->getLocator()->product()->facade());
        };

        $container[self::FACADE_CATEGORY] = function (Container $container) {
            return new ProductManagementToCategoryBridge($container->getLocator()->category()->facade());
        };

        $container[self::FACADE_LOCALE] = function (Container $container) {
            return new ProductManagementToLocaleBridge($container->getLocator()->locale()->facade());
        };

        $container[self::FACADE_TOUCH] = function (Container $container) {
            return new ProductManagementToTouchBridge($container->getLocator()->touch()->facade());
        };

        $container[self::FACADE_URL] = function (Container $container) {
            return new ProductManagementToUrlBridge($container->getLocator()->url()->facade());
        };

        $container[self::SERVICE_UTIL_TEXT] = function (Container $container) {
            return new ProductManagementToUtilTextBridge($container->getLocator()->utilText()->service());
        };

        $container[self::FACADE_TAX] = function (Container $container) {
            return new ProductManagementToTaxBridge($container->getLocator()->tax()->facade());
        };

        $container[self::FACADE_PRODUCT_IMAGE] = function (Container $container) {
            return new ProductManagementToProductImageBridge($container->getLocator()->productImage()->facade());
        };

        $container[self::FACADE_PRICE] = function (Container $container) {
            return new ProductManagementToPriceBridge($container->getLocator()->price()->facade());
        };

        $container[self::FACADE_GLOSSARY] = function (Container $container) {
            return new ProductManagementToGlossaryBridge($container->getLocator()->glossary()->facade());
        };

        $container[self::FACADE_STOCK] = function (Container $container) {
            return new ProductManagementToStockBridge($container->getLocator()->stock()->facade());
        };

        $container[self::QUERY_CONTAINER_CATEGORY] = function (Container $container) {
            return $container->getLocator()->category()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_STOCK] = function (Container $container) {
            return $container->getLocator()->stock()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_PRODUCT_IMAGE] = function (Container $container) {
            return $container->getLocator()->productImage()->queryContainer();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[self::FACADE_PRODUCT] = function (Container $container) {
            return new ProductManagementToProductBridge($container->getLocator()->product()->facade());
        };

        $container[self::FACADE_CATEGORY] = function (Container $container) {
            return new ProductManagementToCategoryBridge($container->getLocator()->category()->facade());
        };

        $container[self::FACADE_LOCALE] = function (Container $container) {
            return new ProductManagementToLocaleBridge($container->getLocator()->locale()->facade());
        };

        $container[self::FACADE_TOUCH] = function (Container $container) {
            return new ProductManagementToTouchBridge($container->getLocator()->touch()->facade());
        };

        $container[self::FACADE_URL] = function (Container $container) {
            return new ProductManagementToUrlBridge($container->getLocator()->url()->facade());
        };

        $container[self::SERVICE_UTIL_TEXT] = function (Container $container) {
            return new ProductManagementToUtilTextBridge($container->getLocator()->utilText()->service());
        };

        $container[self::FACADE_TAX] = function (Container $container) {
            return new ProductManagementToTaxBridge($container->getLocator()->tax()->facade());
        };

        $container[self::FACADE_PRICE] = function (Container $container) {
            return new ProductManagementToPriceBridge($container->getLocator()->price()->facade());
        };

        $container[self::FACADE_PRODUCT_IMAGE] = function (Container $container) {
            return new ProductManagementToProductImageBridge($container->getLocator()->productImage()->facade());
        };

        $container[self::FACADE_GLOSSARY] = function (Container $container) {
            return new ProductManagementToGlossaryBridge($container->getLocator()->glossary()->facade());
        };

        $container[self::FACADE_STOCK] = function (Container $container) {
            return new ProductManagementToStockBridge($container->getLocator()->stock()->facade());
        };

        $container[self::FACADE_MONEY] = function (Container $container) {
            return new ProductManagementToMoneyBridge($container->getLocator()->money()->facade());
        };

        $container[self::QUERY_CONTAINER_CATEGORY] = function (Container $container) {
            return $container->getLocator()->category()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_PRODUCT] = function (Container $container) {
            return $container->getLocator()->product()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_STOCK] = function (Container $container) {
            return $container->getLocator()->stock()->queryContainer();
        };

        $container[self::QUERY_CONTAINER_PRODUCT_IMAGE] = function (Container $container) {
            return $container->getLocator()->productImage()->queryContainer();
        };

        return $container;
    }

}