<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CatalogUrlRewrite\Model;

/**
 * Class to store url rewrites duplicates that were discovered when we save an entity that has related urls
 *
 * This class should be instantiated just once per request
 * Usually products, categories, cms or other extensions
 */
class UrlDuplicatesRegistry
{

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]  */
    private $urlDuplicates = [];

    /**
     * Set the url rewrites duplicates that resulted from a saving process
     *
     * @param array $urlDuplicates
     * @return void
     * @throws \Magento\Framework\Exception\RuntimeException
     */
    public function setUrlDuplicates(array $urlDuplicates)
    {
        if (empty($this->urlDuplicates)) {
            $this->urlDuplicates = $urlDuplicates;
        } else {
            throw new \Magento\Framework\Exception\RuntimeException(__('Url rewrites duplicates can only be set once'));
        }
    }

    /**
     * Returns the stored url rewrites duplicates
     *
     * @return array|\Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function getUrlDuplicates()
    {
        return $this->urlDuplicates;
    }
}
