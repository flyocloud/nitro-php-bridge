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
 * Responsive Images generator:
 *
 * <?= Image::tag((new Responsive('test.jpg'))->add(500, Responsive::PX_OR_LESS, 500, 500)->add(1000, Responsive::PX_OR_MORE, 1000, 1000)); ?>
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

    public static function attributes(string|Responsive $src, $alt, $width = null, $height = null, $format = 'webp', $loading = 'lazy', $decoding = 'async'): string
    {
        if ($src instanceof Responsive) {
            $targetSrc = $src->src;
        } else {
            $targetSrc = $src;
        }

        $image = new Image($targetSrc, $alt, $width, $height, $format, $loading, $decoding);

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

        if ($src instanceof Responsive) {
            $attributes[] = sprintf('srcset="%s"', $src->getSrcset($image));
            $attributes[] = sprintf('sizes="%s"', $src->getSizes());
        }

        return implode(" ", $attributes);
    }

    public static function tag(string|Responsive $src, $alt, $width = null, $height = null, $format = 'webp', $loading = 'lazy', $decoding = 'async', array $options = []): string
    {
        $attributes = self::attributes($src, $alt, $width, $height, $format, $loading, $decoding);

        foreach ($options as $key => $value) {
            $attributes .= sprintf(' %s="%s"', $key, $value);
        }
        return sprintf('<img %s />', $attributes);
    }

    public static function source(string $src, $width = null, $height = null, $format = 'webp'): string
    {
        $image = new Image($src, '', $width, $height, $format);
        return $image->getSrc();
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

    public function getWidth(): ?int
    {
        return empty($this->width) ? null : (int) $this->width;
    }

    public function getHeight(): ?int
    {
        return empty($this->height) ? null : (int) $this->height;
    }

    public function setWidth($width): self
    {
        $this->width = $width;
        return $this;
    }

    public function setHeight($height): self
    {
        $this->height = $height;
        return $this;
    }

    public function getSrc(): string
    {
        $url = str_contains($this->src, 'https://storage.flyo.cloud') ? $this->src : 'https://storage.flyo.cloud/' . $this->src;

        // If either width or height are defined, we add the /thumb/$widthx$height path to it.
        $width = $this->getWidth();
        $height = $this->getHeight();

        if ($width !== null || $height !== null) {
            $width ??= 'null';
            $height ??= 'null';
            $url .= sprintf('/thumb/%sx%s', $width, $height);
        }

        // if the original file name is already in the requested format, we don't add the format to the url.
        $orginalFormat = pathinfo($url, PATHINFO_EXTENSION) ?: '';
        if ($orginalFormat == $this->getFormat()) {
            return $url;
        }

        return $url . "?format=" . $this->getFormat();
    }
}
