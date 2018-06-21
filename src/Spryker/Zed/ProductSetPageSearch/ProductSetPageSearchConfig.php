<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetPageSearch;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductSetPageSearchConfig extends AbstractBundleConfig
{
    /**
     * @return bool
     */
    public function isSendingToQueue()
    {
        return true;
    }

    /**
     * @return null|string
     */
    public function getProductSetSynchronizationPoolName()
    {
        return null;
    }
}
