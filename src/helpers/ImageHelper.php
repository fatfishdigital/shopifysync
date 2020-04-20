<?php

    namespace fatfish\shopifysync\helpers;
    use Craft;
    use craft\commerce\Plugin;
    use craft\elements\Asset as AssetElement;
    use craft\helpers\Assets as AssetsHelper;
    use craft\helpers\FileHelper;
    use craft\helpers\UrlHelper;
    use Mimey\MimeTypes;
    use Sabberworm\CSS\Value\CalcFunction;

    class ImageHelper {

        public static function Download_Image($src) {

            $uploadedAssets = [];
            $folderId=null;
            $element= new \craft\elements\User();
            $field=Craft::$app->getFields()->getFieldByHandle('Image');
            $Temp_Path = self::TempImagePath();
            //try {
                $Explode = explode('?', $src);
                $filename = basename($Explode[0]);
                $fetchedImage = $Temp_Path . $filename;
                // But also check if we've downloaded this recently, use the copy in the temp directory
                $cachedImage = FileHelper::findFiles($Temp_Path, [
                        'only' => [$filename],
                        'recursive' => false,
                ]);
                if (!$cachedImage) {
                    file_put_contents($fetchedImage, file_get_contents($src));

                } else {
                    $fetchedImage = $cachedImage[0];
                }
                $assets = Craft::$app->getAssets();

            $folderId = self::getFolderId($element);
                $folder = $assets->findFolder(['id' => $folderId]);
                $asset = new AssetElement();
                $asset->tempFilePath = $Temp_Path.$filename;
                $asset->filename = $filename;
                $asset->newFolderId = $folder->id;
                $asset->volumeId = $folder->volumeId;
                $asset->avoidFilenameConflicts = true;
                $asset->setScenario(AssetElement::SCENARIO_CREATE);
               $result= Craft::$app->getElements()->saveElement($asset,true);

               return ($asset);

        }

        public static function TempImagePath() {
            $TempPath = Craft::$app->getPath()->getTempPath() . '/shopifyimage/';

            if (!is_dir($TempPath)) {
                FileHelper::createDirectory($TempPath);
            }
            return $TempPath;
        }

        public static function getFolderId($element)
        {

            $assetsService = Craft::$app->getAssets();

            $volumes = Craft::$app->getVolumes();
            $volumehandle=$volumes->getVolumeByHandle('image');
            $volume=$volumes->getVolumeByUid($volumehandle->uid);
            return $assetsService->ensureFolderByFullPathAndVolume("", $volume);
        }
    }
