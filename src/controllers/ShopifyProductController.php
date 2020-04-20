<?php
    namespace fatfish\shopifysync\controllers;
use Craft;
use craft\commerce\elements\Variant;
use craft\commerce\services\ProductTypes;
use craft\web\Controller;
use craft\commerce\elements\Product;
use fatfish\shopifysync\helpers\ImageHelper;


class ShopifyProductController extends Controller {


    public $productHandle="ShopifyProduct";
    public static $ProductType;
    public static $log=[];
    public function init()
    {

        parent::init();
    }

    public static function import_product($Shopifyproduct)
    {


        $product_type = new ProductTypes();
        $product_t=$product_type->getProductTypeByHandle("ShopifyProduct");
        // Create product
        $product = new Product();
        $product->title = $Shopifyproduct->title;
        $product->typeId = $product_t->id;
        $product->enabled = true;
        $product->slug = $Shopifyproduct->title;
        $productvariants=[];
        $optioncount=1;
        foreach($product_t->getVariantFieldLayout()->getFields() as $v):

            $productvariants['option'.$optioncount]=$v->name;

        $optioncount++;

            endforeach;
        $ArrayVaraints=[];
        if(sizeof($productvariants)<=1)
        {
            foreach ($Shopifyproduct->variants as $option):
                $variant = new Variant();
                $variant->sku=$option->sku;
                $variant->price= $option->price;
                $variant->stock= $option->inventory_quantity;
                isset($productvariants['option1']) ? $variant->{$productvariants['option1']}=$option->option1=="Default Title" ? null:$option->option1:null;
                $ArrayVaraints[]=$variant;
            endforeach;
        }
        else
        {

            foreach ($Shopifyproduct->variants as $option):
                $variant = new Variant();
                $variant->sku=$option->sku;
                $variant->price= $option->price;
                $variant->stock= $option->inventory_quantity;
                isset($productvariants['option1']) ? $variant->{$productvariants['option1']}=$option->option1=="Default Title" ? null:$option->option1 :null;
                isset($productvariants['option2']) ? $variant->{$productvariants['option2']}=$option->option2=="Default Title" ? null:$option->option2:null;
                isset($productvariants['option3']) ?  $variant->{$productvariants['option3']}=$option->option3=="Default Title" ? null:$option->option3:null;
                $ArrayVaraints[]=$variant;
            endforeach;
        }


        $product->setVariants($ArrayVaraints);
        if(!is_null($Shopifyproduct->image)) {
            $productImage = ImageHelper::Download_Image($Shopifyproduct->image);
            $product->setFieldValues([
                    'image' => [$productImage->id],
            ]);
        }
        try {
           $result=( Craft::$app->getElements()->saveElement($product));

           if(!$result)
           {

               foreach ($product->getVariants() as $variant) {
                   $errors = $variant->getErrors();
                   foreach ($errors as $error) {
                       ($error[0]);
                       self::$log[]=[
                               'message'=>$error[0],
                                'ProductImport'=>'failed',
                                 'ProductName'=>$product->getName(),

                       ];
                   }
               }

           }
           else
           {
               self::$log[]=[
                       'message'=>'Imported Successfuly',
                       'ProductImport'=>'Success',
                       'ProductName'=>$product->getName(),
               ];
           }
        }
        catch (\Exception $ex)
        {
            return Craft::warning($ex->getMessage());
        }
return self::$log;
    }





    }
