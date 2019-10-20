<?php


namespace Vrpayment\ContaoIntranetBundle\Module;


use Contao\Controller;
use Contao\FilesModel;
use Contao\Image\PictureConfigurationInterface;
use Contao\Module;
use Contao\System;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractModule extends Module
{

    protected function redirectToStep(string $step, string $parameter = '')
    {
        $url = $GLOBALS['objPage']->getAbsoluteUrl('/' . $step);

        if ('' !== $parameter) {
            $url = $url . '?' . $parameter;
        }

        Controller::redirect($url);
    }

    /**
     * @param string $imageSrc
     * @param null   $size
     *
     * @return bool|\stdClass
     */
    protected function getImageObject(string $imageSrc, $size = null)
    {
        /** @var FilesModel $objModel */
        $objModel = \FilesModel::findByUuid($imageSrc);

        if (null === $objModel) {
            return false;
        }

        if (null !== $objModel && is_file(TL_ROOT . '/' . $objModel->path)) {
            // Look at the File
            try {
                $objFile = new \File($objModel->path);
            } catch (\Exception $e) {
                $objFile = new \stdClass();
                $objFile->imageSize = false;
            }

            $imgSize = $objFile->imageSize;

            $size = \StringUtil::deserialize($size);

            if ('' === $size[0] && '' === $size[1] && '' === $size[2]) {
                $size[0] = (int) $imgSize[0] - 1;
            }

            if (is_numeric($size)) {
                $size = [0, 0, (int) $size];
            } elseif (!$size instanceof PictureConfigurationInterface) {
                if (!\is_array($size)) {
                    $size = [];
                }

                $size += [0, 0, 'crop'];
            }

            if (TL_MODE === 'BE') {
                $size = [600, 400, 'crop'];
            }

            // Generate Images
            try {
                $container = \System::getContainer();
                // $staticUrl = $container->get('contao.assets.files_context')->getStaticUrl();
                $src = \System::getContainer()->get('contao.image.image_factory')->create(TL_ROOT . '/' . $objFile->path, $size)->getUrl(TL_ROOT);
                $picture = $container->get('contao.image.picture_factory')->create(TL_ROOT . '/' . $objModel->path, $size);
            } catch (\Exception $e) {
                \System::log('Image "' . $objModel->path . '" could not be processed: ' . $e->getMessage(), __METHOD__, TL_ERROR);

                $src = '';
                $picture = ['img' => ['src' => '', 'srcset' => ''], 'sources' => []];
            }

            // Load the meta data
            if ($objModel instanceof FilesModel) {
                if (TL_MODE === 'FE') {
                    global $objPage;

                    $arrMeta = \Frontend::getMetaData($objModel->meta, $objPage->language);

                    if (empty($arrMeta) && null !== $objPage->rootFallbackLanguage) {
                        $arrMeta = \Frontend::getMetaData($objModel->meta, $objPage->rootFallbackLanguage);
                    }
                } else {
                    $arrMeta = \Frontend::getMetaData($objModel->meta, $GLOBALS['TL_LANGUAGE']);
                }

                \Controller::loadDataContainer('tl_files');

                // Add any missing fields
                foreach (array_keys($GLOBALS['TL_DCA']['tl_files']['fields']['meta']['eval']['metaFields']) as $k) {
                    if (!isset($arrMeta[$k])) {
                        $arrMeta[$k] = '';
                    }
                }

                $arrMeta['imageTitle'] = $arrMeta['title'];
                $arrMeta['imageUrl'] = $arrMeta['link'];
                unset($arrMeta['title'], $arrMeta['link']);
            }

            $objImage = new \stdClass();
            $objImage->img = $picture->getImg(TL_ROOT);
            $objImage->sources = $picture->getSources(TL_ROOT);
            $objImage->meta = $arrMeta;

            return $objImage;
        }

        return false;
    }

}
