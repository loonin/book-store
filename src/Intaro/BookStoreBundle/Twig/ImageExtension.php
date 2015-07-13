<?php

namespace Intaro\BookStoreBundle\Twig;

use Gregwar\Image\Image;

class ImageExtension extends \Twig_Extension
{
    protected $params,
              $rootDir;

    public function __construct($params, $rootDir)
    {
        $this->params = $params;
        $this->rootDir = $rootDir;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('resize', array($this, 'resizeFilter')),
        );
    }

    public function resizeFilter($pathToImage, $width = null, $height = null)
    {
        if (!$width && !$height) {
            $width = $this->params['width'];
            $height = $this->params['height'];
        }

        $path = '/' . Image::open($this->rootDir . '/../web' . $pathToImage)
                ->zoomCrop($width, $height, 'transparent', 'top', 'left')
                ->guess();

        return $path;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'image_extension';
    }
}