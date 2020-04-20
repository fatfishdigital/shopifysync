<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\records;

use fatfish\shopifysync\ShopifySync;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
class ShopifySyncRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shopifysync_shopifysyncrecord}}';
    }
}
