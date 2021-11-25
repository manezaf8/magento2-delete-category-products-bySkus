<?php
/**
 * @package   F8\Catalog\Block\Adminhtml\Category\Merchandiser
 * @author    Ntabethemba Mabetshe
 * @date      12-11-2021
 * @copyright Copyright © 2021  Group IT
 */

declare(strict_types=1);

namespace F8\Catalog\Block\Adminhtml\Category\Merchandiser;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Magento\VisualMerchandiser\Model\Category\Products;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\VisualMerchandiser\Block\Adminhtml\Category\Merchandiser\Grid;

/**
 * Class Grid
 * @package F8\Catalog\Block\Adminhtml\Category\Merchandiser\VMGrid
 */
class VMGrid extends Grid implements ButtonProviderInterface
{
    /**
     * @var Products
     * @since 100.1.0
     */
    protected $products;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Context $context
     * @var Data $backendHelper
     * @var Registry $coreRegistry
     * @var Products $products
     * @var array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        Products $products,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct(
            $context, $backendHelper, $coreRegistry, $products, $data
        );
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * @return void
     * @since 100.1.0
     */
    protected function _construct()
    {
        parent::_construct();
    }
  
    /**
     * Export csv button
     *
     * @return array
     */
    public function getButtonData(){
         $categoryId = $this->getCategoryId();
 
         if ($categoryId) {
             return [
                 'id' => 'sku_export',
                 'label' => __('Export Skus'),
                 'name' => 'sku_export',
                 'on_click' => "deleteConfirm('" .__('Download file with SKUs ?') ."', '"
                     . $this->getExportUrl() . "', {data: {}})",
                 'class' => 'export action-secondary',
                 'sort_order' => 25
             ];
         }
 
         return [];
     }

     /**
      * Get Category Id
      *
      * @return int
      */
    public function getCategoryId(){
        $category = $this->coreRegistry->registry('current_category');
        $categoryId =  $category->getId();
        return $categoryId;
    }

    /**
     * Get Defualt Parameters
     *
     * @return array
     */
    protected function getDefaultUrlParams(){
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }

    /**
     * Get Export Data url
     *
     * @param array $args
     * @return path
     */
    public function getExportUrl(array $args = []){
        $params = array_merge($this->getDefaultUrlParams(), $args);
        
        return $this->getUrl('f8catalog/*/skuexport', $params);
    }
}