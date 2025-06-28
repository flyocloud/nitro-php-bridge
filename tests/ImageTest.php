<?php

use Flyo\Bridge\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testImageConstructor()
    {
        $image = new Image('test.jpg', 'Test Image', 100, 200, 'png', 'auto', 'sync');

        $this->assertEquals('https://storage.flyo.cloud/test.jpg/thumb/100x200?format=png', $image->getSrc());
    }

    public function testDefaultValues()
    {
        $this->assertSame('src="https://storage.flyo.cloud/foo.png?format=webp" alt="alt" loading="lazy" decoding="async"', Image::attributes('foo.png', 'alt'));
    }

    public function testImageTag()
    {
        $tag = Image::tag('test.jpg', 'Test Image', 100, 200, 'png', 'auto', 'sync');

        $this->assertEquals('<img src="https://storage.flyo.cloud/test.jpg/thumb/100x200?format=png" alt="Test Image" loading="eager" decoding="sync" width="100" height="200" />', $tag);
    }

    public function testImageOptions()
    {
        $tag = Image::tag('test.jpg', 'Test Image', 100, 200, 'png', 'auto', 'sync', ['class' => 'img-fluid', 'foo' => 1]);

        $this->assertEquals('<img src="https://storage.flyo.cloud/test.jpg/thumb/100x200?format=png" alt="Test Image" loading="eager" decoding="sync" width="100" height="200" class="img-fluid" foo="1" />', $tag);
    }

    public function testAltTagEncoding()
    {
        $this->assertSame('<img src="https://storage.flyo.cloud/foo.jpg?format=webp" alt="&lt;h1&gt;hoi&lt;/h1&gt;" loading="lazy" decoding="async" />', Image::tag('foo.jpg', '<h1>hoi</h1>'));
    }

    public function testSourceOnly()
    {
        $this->assertSame('https://storage.flyo.cloud/foobar.jpg/thumb/200x200?format=webp', Image::source('foobar.jpg', 200, 200));
        $this->assertSame('https://storage.flyo.cloud/foobar.jpg?format=webp', Image::source('foobar.jpg'));
        $this->assertSame('https://storage.flyo.cloud/foobar.jpg', Image::source('foobar.jpg', null, null, 'jpg'));
    }

    public function testExternalOtherSource()
    {
        $this->assertSame('https://example.com/foobar.jpg', Image::source('https://example.com/foobar.jpg', 400, 400));
        $this->assertSame('http://localhost:8080/foobar.jpg', Image::source('http://localhost:8080/foobar.jpg', 400, 400));
        $this->assertSame('https://storage.flyo.cloud/foobar.jpg/thumb/400x400?format=webp', Image::source('https://storage.flyo.cloud/foobar.jpg', 400, 400));
        $this->assertSame('https://storage.flyo.cloud/foobar.jpg/thumb/400x400?format=webp', Image::source('foobar.jpg', 400, 400));
        $this->assertSame('/foobar.jpg', Image::source('/foobar.jpg', 400, 400));
    }

    public function testFromObject()
    {
        $image = Image::fromObject((object)[
            'source' => 'test.jpg',
            'caption' => 'Test Image',
        ], 100, 100);

        $this->assertSame('https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp', $image->getSrc());
        $this->assertSame('src="https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp" alt="Test Image" loading="lazy" decoding="async" width="100" height="100"', $image->toAttributes());
        $this->assertSame('<img src="https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp" alt="Test Image" loading="lazy" decoding="async" width="100" height="100" />', $image->toTag());
    }

    public function testFromCaptionButOwnAltTag()
    {
        $image = Image::fromObject((object)[
            'source' => 'test.jpg',
        ], 100, 100, 'Own Alt Tag');

        $this->assertSame('https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp', $image->getSrc());
        $this->assertSame('src="https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp" alt="Own Alt Tag" loading="lazy" decoding="async" width="100" height="100"', $image->toAttributes());
        $this->assertSame('<img src="https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp" alt="Own Alt Tag" loading="lazy" decoding="async" width="100" height="100" />', $image->toTag());
    }

    public function testFromCaptionButOwnAltTagWithEncoding()
    {
        $image = Image::fromObject((object)[
            'source' => 'test.jpg',
        ], 100, 100, '"Test Encoding"');

        $this->assertSame('&quot;Test Encoding&quot;', $image->getAlt());
        $this->assertSame('<img src="https://storage.flyo.cloud/test.jpg/thumb/100x100?format=webp" alt="&quot;Test Encoding&quot;" loading="lazy" decoding="async" width="100" height="100" />', $image->toTag());
    }

    /*
    public function testGetSrc()
    {
        $image = new Image('test.jpg', 'Test Image');
        $this->assertEquals('https://storage.flyo.cloud/test.jpg', $image->getSrc());

        $image = new Image('https://storage.flyo.cloud/test.jpg', 'Test Image');
        $this->assertEquals('https://storage.flyo.cloud/test.jpg', $image->getSrc());
    }
    */
}
