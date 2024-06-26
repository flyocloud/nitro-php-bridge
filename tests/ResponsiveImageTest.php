<?php

use Flyo\Bridge\BreakpointType;
use Flyo\Bridge\Image;
use Flyo\Bridge\Responsive;
use PHPUnit\Framework\TestCase;

class ResponsiveImageTest extends TestCase
{
    public function testImageConstructor()
    {
        $responsive = (new Responsive('test.jpg'))
            ->add(400, BreakpointType::PX_OR_LESS, 400, 400)
            ->add(1000, BreakpointType::PX_OR_MORE, 1200, null);

        $image = Image::attributes($responsive, 'Test Image', 100, 200, 'png', 'auto', 'sync');

        $this->assertEquals('src="https://storage.flyo.cloud/test.jpg/thumb/100x200?format=png" alt="Test Image" loading="eager" decoding="sync" width="100" height="200" srcset="https://storage.flyo.cloud/test.jpg/thumb/400x400?format=png 400w, https://storage.flyo.cloud/test.jpg/thumb/1200xnull?format=png 1000w" sizes="(max-width: 400px) 33vw, (min-width: 1000px) 83vw, 100vw"', $image);
    }

    public function testSingleResponsiveImageWithRawOriginalSizeFallback()
    {
        $responsive = (new Responsive('test.jpg'))
            ->add(400, BreakpointType::PX_OR_LESS, 400, 400);

        $image = Image::attributes($responsive, 'Test Image', 1000, 800, 'png', 'auto', 'sync');

        $this->assertEquals('src="https://storage.flyo.cloud/test.jpg/thumb/1000x800?format=png" alt="Test Image" loading="eager" decoding="sync" width="1000" height="800" srcset="https://storage.flyo.cloud/test.jpg/thumb/400x400?format=png 400w, https://storage.flyo.cloud/test.jpg/thumb/1000x800?format=png 1000w" sizes="(max-width: 400px) 40vw, 100vw"', $image);
    }
}
