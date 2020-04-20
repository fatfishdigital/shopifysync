<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\controllers;
use Craft;
use craft\web\Controller;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
use \fatfish\shopifysync\services\ShopifySyncService as shopify;
class ShopifyController extends Controller
{


    protected $allowAnonymous = ['index', 'do-something'];
    private $_Products;
    public $title;
    public $handle;
    public $sku;
    public $description;
    public $variants=[];



        public function init() {

                $Products = new shopify();
                $this->_Products = json_decode($Products->fetch_products());
        }

        /**
         * @return mixed
         */
        public function actionFetchProduct()
        {


            if(Craft::$app->getRequest()->isAjax)
            {
                $results='';

            foreach ($this->_Products->products as $shopifyproduct):

                $product = new \stdClass();
                $product->title=$shopifyproduct->title;
                $product->handle=$shopifyproduct->handle;
                $product->description = $shopifyproduct->body_html;
                $product->variants=$shopifyproduct->variants;
                $product->image=isset($shopifyproduct->images[0]->src) ? $shopifyproduct->images[0]->src:null;
                try {
                   $results= ShopifyProductController::import_product($product);
                }
                catch (\Exception $ex)
                {
                    $results=Craft::info($ex->getMessage());
                }
                endforeach;
            return json_encode($results);
        }

        }


}
