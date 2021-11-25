<?php
/**
 * @package   F8GroupEcom\F8Catalog\Setup\Patch\Data
 * @author    Ntabethemba Mabetshe 
 * @date      10-11-2021
 * @copyright Copyright © 2021 F8 Group IT
 */

declare(strict_types=1);

namespace F8GroupEcom\F8Catalog\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Class AddBulkUnlinkAttribute
 * @package F8GroupEcom\F8Catalog\Setup\Patch\Data
 */
class AddBulkUnlinkAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Applying Bulk Unlink Custom EAV Attribute
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'bulk_unlink',
            [
                'type' => 'text',
                'label' => 'Bulk Products Remove',
                'input' => 'textarea',
                'source' => '',
                'frontend' => '',
                'required' => false,
                'backend' => '',
                'default' => null,
                'user_defined' => true,
                'unique' => false,
                'group' => 'General',
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Revert
     * @return void
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'bulk_unlink');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Aliases
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Dependencies
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}