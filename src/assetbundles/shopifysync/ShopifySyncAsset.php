<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\assetbundles\ShopifySync;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
class ShopifySyncAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@fatfish/shopifysync/assetbundles/shopifysync/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ShopifySync.js',
        ];

        $this->css = [
            'css/ShopifySync.css',
        ];

        parent::init();
    }
}
