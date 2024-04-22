<?php

namespace Flyo\Bridge;

/**
 *
 * When the screen is 500 Pixel or Less, use the 500x500 image
 *
 * [500, self::PX_OR_LESS, 500, 500]
 *
 * @see https://dev.flyo.cloud/dev/infos/images
 */
class Responsive
{
    public const PX_OR_LESS = 'max-width';

    public const PX_OR_MORE = 'min-width';

    private $_srcset = [];

    public function __construct(public string $src)
    {

    }

    public function add(string $screenSize, BreakpointType $type, ?int $width = null, ?int $height = null): self
    {
        $this->_srcset[] = [$screenSize, $type, $width, $height];
        return $this;
    }

    public function getSrcset(Image $image): string
    {
        $originalWidth = $image->getWidth();
        $hasLargeImage = false;
        $fallback = $image->getSrc() . ' ' . $originalWidth . 'w';
        $srcset = [];
        foreach ($this->_srcset as $src) {
            /** @var BreakpointType $type */
            $type = $src[1];
            $srcset[] = $type->srcset($src[0], $image->setWidth($src[2])->setHeight($src[3])->getSrc());

            if ($src[2] > $originalWidth) {
                $hasLargeImage = true;
            }
        }
        if (!$hasLargeImage) {
            $srcset[] = $fallback;
        }
        return implode(", ", $srcset);
    }

    public function getSizes(): string
    {
        $sizes = [];
        foreach ($this->_srcset as $src) {
            /** @var BreakpointType $type */
            $type = $src[1];
            $sizes[] = $type->size($src[0]);
        }
        $sizes[] = '100vw';
        return implode(", ", $sizes);
    }
}
