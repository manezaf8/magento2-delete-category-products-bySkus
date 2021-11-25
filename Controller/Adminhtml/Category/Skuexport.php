<?php
/**
 * @package   F8\Catalog\Controller\Adminhtml\Category
 * @author    Ntabethemba Mabetshe
 * @date      12-11-2021
 * @copyright Copyright © 2021 Group IT
 */
namespace F8\Catalog\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Controller\Adminhtml\Category;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\File\Csv;

/**
 * Class Skuexport
 * @package F8\Catalog\Controller\Adminhtml\Category
 */
class Skuexport extends Category
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param ProductFactory $productFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param Csv $csvProcessor
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CollectionFactory $productCollectionFactory,
        FileFactory $fileFactory,
        ProductFactory $productFactory,
        LayoutFactory $resultLayoutFactory,
        Csv $csvProcessor,
        DirectoryList $directoryList
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->productFactory = $productFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;

        parent::__construct($context);
    }

    /**
     * CSV Create and Download
     *
     * @return ResponseInterface
     * @throws FileSystemException
     */
    public function execute(){
        $category = $this->_initCategory();
        $categoryId = (int)$category->getId();
        $categoryName = $category->getName();
        $content[] = [
            'sku' => __('Sku'),
            'id' => __('Product id'),
        ];

        $product = $this->productFactory->create()->getCollection();
        $collection = $this->getProductCollectionByCategories($categoryId);
        $fileName = $categoryName. ' product_skus.csv'; 
        $filePath =  $this->directoryList->getPath(DirectoryList::MEDIA) . "/" . $fileName;

        while ($product = $collection->fetchItem()) {
            $content[] = [
                $product->getSku(),
                $product->getId()
            ];
        }

        $this->csvProcessor->setEnclosure('"')->setDelimiter(',')->saveData($filePath, $content);

        return $this->fileFactory->create(
            $fileName,
            [
                'type'  => "filename",
                'value' => $fileName,
                'rm'    => true, 
            ],
            DirectoryList::MEDIA,
            'text/csv',
            null
        );
    }

    /**
     * Get products by category id
     *
     * @param $ids
     * @return productCollection
     */
    public function getProductCollectionByCategories($ids){
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('id');
        $collection->addCategoriesFilter(['in' => $ids]);

        return $collection;
    }
}