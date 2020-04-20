<?php
/**
 * ShopifySync plugin for Craft CMS 3.x
 *
 * Import and Export Products from Shopify
 *
 * @link      www.fatfish.com.au
 * @copyright Copyright (c) 2020 Fatfish
 */

namespace fatfish\shopifysync\services;

use craft\feeds\GuzzleClient;
use fatfish\shopifysync\ShopifySync;

use Craft;
use craft\base\Component;
use GuzzleHttp\Client;

/**
 * @author    Fatfish
 * @package   ShopifySync
 * @since     1.0.0
 */
class ShopifySyncService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public $productapi='/admin/api/2020-04/products.json';
    public $apikey;
    public $apiurl;
    public $secret;
    public $password;
    public $shopify_products;
    public $shopify_variants=[];



    public function init() {

            $api_settings   = ShopifySync::$plugin->getSettings();
            $this->apikey   =     $api_settings->apikey;
            $this->apiurl   =     trim($api_settings->apiurl.$this->productapi);
            $this->secret   =     $api_settings->secret;
            $this->password =    $api_settings->password;
            $client = new Client();
            try {
                $results=$client->request('GET',$this->apiurl,['auth'=>[$this->apikey,$this->password]]);

            }
            catch (\Exception $ex)
            {
                return $ex->getMessage();
            }


            if($results->getStatusCode()==200) {
                 $result=$results->getBody()->getContents();
                 $this->shopify_products=$result;

            }else
            {
                return false;
            }

    }

    public  function fetch_products()
    {

        return $this->shopify_products;

    }

    public function fetch_variants(){


            $product_variants = json_decode($this->shopify_products);
            if(is_null($product_variants))
            {
                Craft::info("Please configure shopify endpoints");
                return;
            }

      foreach ($product_variants->products as $product_options):

        foreach($product_options->options as $options):

            if($options->name!="Title")
            {
                $this->shopify_variants[$options->name]=$options->name;
            }

            endforeach;

          endforeach;

          return array_values($this->shopify_variants);

    }

}
