<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\models;

use fatfish\shopifysync\ShopifySync;

use Craft;
use craft\base\Model;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $apikey = 'Enter Shopify API Key Here';
    public $password = 'Enter API Password';
    public $secret = 'Enter Shared Secret';
    public $apiurl = 'Enter API End point URl';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['apikey', 'string'],
            ['apikey', 'default', 'value' => 'API KEY'],
            ['password','string'],
            ['password','default','value'=>'Password'],
            ['secret','string'],
            ['secret','default'],
            ['apiurl','string'],
            ['apiurl','default'],

        ];
    }
}
