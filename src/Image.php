<?php

namespace Flyo\Bridge;

/**
 * The main intent for the image class is that if you define width and height, the image will be
 * automatically resized to the given dimensions trough the flyo storage service but also define
 * the width and height attributes in the html tag which allows good browser support for lazy loading
 * which is included as well. Therefore its recommend to always defined width and height.
 *
 * ```php
 * <div><?= Image::tag('test.jpg', 'Test Image', 300, 300); ?></div>
 * ```
 *
 * Or only attributes:
 *
 * ```php
 * <img <?= Image::attributes('test.jpg', 'Test Image', 300, 300); ?> />
 * ```
 *
 * @see https://dev.flyo.cloud/dev/infos/images
 */
class Image
{
    public function __construct(
        protected string $src,
        protected string $alt,
        protected ?int $width = null,
        protected ?int $height = null,
        protected string $format = 'web',
        protected string $loading = 'lazy',
        protected string $decoding = 'async'
    ) {
    }

    public static function attributes($src, $alt, $width = null, $height = null, $format = 'webp', $loading = 'lazy', $decoding = 'async')
    {
        $image = new Image($src, $alt, $width, $height, $format, $loading, $decoding);

        $attributes = [
            sprintf('src="%s"', $image->getSrc()),
            sprintf('alt="%s"', $image->getAlt()),
            sprintf('loading="%s"', $image->getLoading()),
            sprintf('decoding="%s"', $image->getDecoding()),
        ];

        if ($image->getwidth()) {
            $attributes[] = sprintf('width="%s"', $image->getwidth());
        }

        if ($image->getHeight()) {
            $attributes[] = sprintf('height="%s"', $image->getHeight());
        }

        return implode(" ", $attributes);
    }

    public static function tag($src, $alt, $width = null, $height = null, $format = 'webp', $loading = 'lazy', $decoding = 'async')
    {
        $attributes = self::attributes($src, $alt, $width, $height, $format, $loading, $decoding);
        return sprintf('<img %s />', $attributes);
    }

    /**
     * @see https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/loading
     */
    public function getLoading(): string
    {
        return strtolower($this->loading) === 'lazy' ? 'lazy' : 'eager';
    }

    /**
     * @see https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/decoding#sync
     */
    public function getDecoding(): string
    {
        $decoding = strtolower($this->decoding);

        if (!in_array($decoding, ['async', 'auto', 'sync'])) {
            $decoding = 'auto';
        }

        return $decoding;
    }

    public function getFormat(): string
    {
        $format = strtolower($this->format);

        if (!in_array($format, ['webp', 'png', 'jpg', 'jpeg', 'gif'])) {
            $format = 'webp';
        }

        return $format;
    }

    public function getAlt(): string
    {
        return htmlspecialchars($this->alt, ENT_QUOTES);
    }

    public function getwidth(): ?int
    {
        return empty($this->width) ? null : (int) $this->width;
    }

    public function getHeight(): ?int
    {
        return empty($this->height) ? null : (int) $this->height;
    }

    public function getSrc(): string
    {
        $url = str_contains($this->src, 'https://storage.flyo.cloud') ? $this->src : 'https://storage.flyo.cloud/' . $this->src;

        // If either width or height are defined, we add the /thumb/$widthx$height path to it.
        $width = $this->getwidth();
        $height = $this->getHeight();

        if ($width !== null || $height !== null) {
            $width ??= 'null';
            $height ??= 'null';
            $url .= sprintf('/thumb/%sx%s', $width, $height);
        }

        return $url . "?format=" . $this->getFormat();
    }
}
