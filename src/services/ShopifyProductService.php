<?php

    namespace fatfish\shopifysync\services;
    use Craft;
    use craft\base\Component;
    use craft\commerce\models\ProductType as ProductTypeModel;
    use craft\commerce\models\ProductTypeSite as ProductTypeSiteModel;
    use craft\commerce\Plugin;
    use craft\commerce\records\ProductType as product;
    use craft\commerce\services\ProductTypes as ProductService;
    use craft\db\Query;
    use craft\models\FieldLayout;
    use craft\records\FieldLayoutField;
    use craft\records\FieldLayoutTab;
    use craft\records\Site;
    use fatfish\shopifysync\records\ShopifySyncRecord;


    class ShopifyProductService extends Component{


        public static $FieldLayoutRecord;
        public static $FieldLayoutTab;

        public function init()
        {

        }

        /**
         * create new product Type shopify
         * @param $handle
         */
        public static function create_shopify_product_type($handle)
        {

            $fieldyaout=self::create_product_image_field();

            $data = [
                    'name' => $handle,
                    'handle' => $handle,
                    'hasDimensions' => false,
                    'hasVariants' => true,
                    'hasVariantTitleField' => false,
                    'titleFormat' => '{product.title}',
                    'skuFormat' => '',
                    'descriptionFormat' => '',




            ];

            $productType = new ProductTypeModel($data);
            $productType->setFieldLayoutId($fieldyaout->id);

            $siteIds = (new Query())
                    ->select(['id'])
                    ->from(Site::tableName())
                    ->column();

            $allSiteSettings = [];

            foreach ($siteIds as $siteId) {
                $siteSettings = new ProductTypeSiteModel();

                $siteSettings->siteId = $siteId;
                $siteSettings->hasUrls = true;
                $siteSettings->uriFormat = 'shop/products/{slug}';
                $siteSettings->template = 'shop/products/_product';

                $allSiteSettings[$siteId] = $siteSettings;
            }

            $productType->setSiteSettings($allSiteSettings);
            Plugin::getInstance()->getProductTypes()->saveProductType($productType);

        }

        public static function shopify_product_Field_type($shopifyFieldtype,$producthandle)
        {

            $product_options = $shopifyFieldtype->fetch_variants();



            if(is_null($product_options))
            {
                self::create_variant_layout();
            }
            $product_options = $shopifyFieldtype->fetch_variants();
            $Fields = new \craft\services\Fields();
            $fieldsService = Craft::$app->getFields();
            foreach ($product_options as $key=>$value):
                $does_field_exist= $Fields->getFieldByHandle($value);
                if(!isset($does_field_exist))
                {

                    $field = $fieldsService->createField([
                            'type' => 'craft\fields\PlainText',
                            'groupId' => 1,
                            'name' => $value,
                            'handle' => $value,
                            'instructions' => '',
                            'translationMethod' => 'none',
                            'translationKeyFormat' => NULL,
                            'settings' => [
                                    'placeholder' => $value,
                                    'charLimit' => '',
                                    'multiline' => '',
                                    'initialRows' => '4',
                                    'columnType' => 'text',
                            ],
                    ]);
                    Craft::$app->getFields()->saveField($field);

                }
                endforeach;




            $FieldLayoutData = ShopifySyncRecord::find()->one();
            self::shopify_product_field_layout($product_options,$producthandle,$FieldLayoutData);

        }

//TODO: Need to optimize this code.
        public static function shopify_product_field_layout($layouthandle,$productHandle,$FieldLayoutData)
        {

            $ShopifyProductService= new ProductService();
            $ProductType = new product();
            $Shopify_Product = $ShopifyProductService->getProductTypeByHandle($productHandle);
            $p = $ProductType::findOne(['id'=>$Shopify_Product->id]);
            $p->id = $Shopify_Product->id;
            $p->name = $Shopify_Product->name;
            $p->handle = $Shopify_Product->handle;
            $p->titleFormat = $Shopify_Product->titleFormat;

           foreach ($layouthandle as $fields):
               $field=(Craft::$app->getFields()->getFieldByHandle($fields));
               $FieldLayoutFields = new FieldLayoutField();
               $FieldLayoutFields->layoutId=$FieldLayoutData->FieldLayoutRecord;
               $FieldLayoutFields->tabId=$FieldLayoutData->FieldLayoutTab;
               $FieldLayoutFields->fieldId=$field->id;
               $FieldLayoutFields->save();
               $p->variantFieldLayoutId=$FieldLayoutFields->layoutId;
               $p->update();
            endforeach;





        }


        public static function create_variant_layout()
        {
            self::$FieldLayoutRecord= new \craft\records\FieldLayout();
            self::$FieldLayoutRecord->type=\craft\commerce\elements\Variant::class;
            self::$FieldLayoutRecord->save();

            self::$FieldLayoutTab = new FieldLayoutTab();
            self::$FieldLayoutTab->layoutId=self::$FieldLayoutRecord->id;
            self::$FieldLayoutTab->name="Content";
            self::$FieldLayoutTab->save();


            $ShopifySyncRecord = new ShopifySyncRecord();
            $ShopifySyncRecord->FieldLayoutRecord=self::$FieldLayoutRecord->id;
            $ShopifySyncRecord->FieldLayoutTab=self::$FieldLayoutTab->id;
            $ShopifySyncRecord->save();

            return;
        }

        public static function create_product_image_field()
        {
            $imageLayout = new FieldLayout();
            $imageLayout->type=\craft\commerce\elements\Product::class;
            Craft::$app->getFields()->saveLayout($imageLayout);

            $productFieldlayoutTabService = new FieldLayoutTab();
            $productFieldlayoutTabService->layoutId=$imageLayout->id;
            $productFieldlayoutTabService->name="Common";
            $productFieldlayoutTabService->save();

            $fieldsService = Craft::$app->getFields();

            $product_Image_field = $fieldsService->createField([

                    'type'=> 'craft\fields\Assets',
                    'groupId'=>1,
                    'name' => 'Image',
                    'handle' => 'image',
                    'instructions' => '',
                    'translationMethod' => 'none',
                    'translationKeyFormat' => NULL,
                    'settings' => [


                    ],
                            ]
            );

            Craft::$app->getFields()->saveField($product_Image_field);
            $Image_Field=Craft::$app->getFields()->getFieldByHandle('Image');


            $FieldlayoutField = new FieldLayoutField();
            $FieldlayoutField->layoutId=$imageLayout->id;
            $FieldlayoutField->fieldId=$Image_Field['id'];
            $FieldlayoutField->tabId=$productFieldlayoutTabService->id;
            $FieldlayoutField->save();

            return $imageLayout;
        }

    }
