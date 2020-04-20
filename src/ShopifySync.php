<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync;

use craft\commerce\helpers\Product;
use craft\models\CategoryGroup;
use craft\models\FieldLayout;
use craft\records\FieldLayoutTab;
use craft\services\Fields;
use fatfish\shopifysync\fieldtypes\ShopifyFieldType;
use fatfish\shopifysync\services\ShopifyProductService;
use fatfish\shopifysync\services\ShopifySyncService;
use fatfish\shopifysync\services\ShopifySyncService as ShopifySyncServiceService;
use fatfish\shopifysync\models\Settings;
use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use Psy\Input\CodeArgument;
use yii\base\Event;
use function GuzzleHttp\Psr7\try_fopen;

/**
 * Class ShopifySync
 *
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 *
 * @property  ShopifySyncServiceService $shopifySyncService
 */
class ShopifySync extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var ShopifySync
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {

                $event->rules['index'] = 'shopify-sync/shopify';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['fetchproduct'] = 'shopify-sync/shopify/fetch-product';
            }
        );
        Event::on(self::class,self::EVENT_AFTER_SAVE_SETTINGS,function(){
            ShopifyProductService::shopify_product_Field_type($this->shopifySyncService,"ShopifyProduct");
        });

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                  ShopifyProductService::create_shopify_product_type("ShopifyProduct");

                }
            }
        );
        Craft::info(
            Craft::t(
                'shopify-sync',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );


    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'shopify-sync/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
