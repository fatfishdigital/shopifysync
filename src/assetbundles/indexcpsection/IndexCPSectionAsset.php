<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\assetbundles\indexcpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
class IndexCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@fatfish/shopifysync/assetbundles/indexcpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [

            'https://cdn.jsdelivr.net/npm/vue',
                'js/Index.js',
        ];

        $this->css = [
            'css/Index.css',
        ];

        parent::init();
    }
}
