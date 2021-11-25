<?php
/**
 * @package   F8\Catalog\Observer
 * @author    Ntabethemba Mabetshe
 * @date      10-11-2021
 * @copyright Copyright © 2021  Group IT
 */

namespace F8\Catalog\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogCategoryUnlinkProductSaveObserver
 * @package F8\Catalog\Observer
 */
class CatalogCategoryUnlinkProductSaveObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * CategoryPlugin constructor.
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param Observer $observer
     * @return array|void
     */
    public function execute(Observer $observer)
    {
        /** @var CategoryInterface $category */
        $category = $observer->getCategory();
        /** @var RequestInterface $request */
        $request = $observer->getRequest();

        if (array_key_exists('bulk_unlink', $request->getParams())) {
            $bulkUnlink =  $request->getParam('bulk_unlink');

            if ($bulkUnlink === '' ) {
                return $this;
            }
        
            // convect skus from string to array
            $unlinkProductSkus = $this->prepareData($bulkUnlink);
            //The hero product check before product unlink logic (Hero product will be ignored)
            $postedUnlinkProducts = $category->getPostedProducts(); 
            $unlinkProductCollection = $this->productCollectionFactory->create() 
                ->addAttributeToSelect(['entity_id', 'sku'])
                ->addAttributeToFilter(
                'sku', $unlinkProductSkus
            );

            if (is_null($unlinkProductCollection) || $postedUnlinkProducts === null) {
                return;
            }

            $unlinkPositions = [];
            $flippedUnlinkProductSkus = array_flip($unlinkProductSkus);

            /* @var $unlinkProduct ProductInterface */
            foreach ($unlinkProductCollection as $unlinkProduct) { 
                if (array_key_exists($unlinkProduct->getSku(), $flippedUnlinkProductSkus)) { 
                    $unlinkPositions[$unlinkProduct->getId()] = $flippedUnlinkProductSkus[$unlinkProduct->getSku()];
                }
            }

            //flip the position again to unlink with the value
            $flippedPosition = array_flip($unlinkPositions);

            foreach ($flippedPosition as $position => $value) {
                if(isset( $postedUnlinkProducts[$value])){
                    unset($postedUnlinkProducts[$value]);
                }
            }
            $category->setBulkUnlink(); //empty the box
            $category->setPostedProducts($postedUnlinkProducts); 
        }
    }

    /**
     * Prepare array data 
     * 
     * @param string $actionProductsData
     * @return array
     */
    private function prepareData(string $actionProductsData) {
        $actionProductSkus = explode(',', $actionProductsData);
        $actionProductSkus = array_filter($actionProductSkus, 'strlen');
        $actionProductSkus = array_unique($actionProductSkus);
        $actionProductSkus = array_splice($actionProductSkus, 0);

        return $actionProductSkus;
    }
}
