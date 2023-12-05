<?php declare(strict_types=1);

namespace Flyo\Bridge\Tests;

use Flyo\Bridge\Wysiwyg;
use Nadar\ProseMirror\Node;
use Nadar\ProseMirror\Types;
use PHPUnit\Framework\TestCase;

class WysiwygTest extends TestCase
{
    public function testCompile()
    {
        $path = __DIR__ . '/example1.json';
        $buff = file_get_contents($path);
        $json = json_decode($buff, true);

        $content = Wysiwyg::render($json, function(Wysiwyg $parser) {
            $parser
                ->replaceNode(Types::image, fn (Node $node) => "<img class=\"w-full\" src=\"{$node->getAttr('src')}\" alt=\"{$node->getAttr('alt')}\">")
                ->addNode('accordion', fn (Node $node) => "<span>{$node->getAttr('title')}</span>");
        });

        $expected = '<p>Das ist ein Test.</p><span>Markenstrategie</span><p></p><span>Das ist unser Ziel?</span><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<div>hardBreak does not exists. </div>NEWLINEBREAK....?</p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p><p></p><p></p><p>Some empty p&apos;s above</p><p>Test</p><blockquote><p>asdfasdfasdf</p></blockquote><img class="w-full" src="https://storage.flyo.cloud/zusammenstellungv03_32528f65.gif/thumb/1400xnull" alt=""><p></p><p>Wohing geht <a href="mailto:foobar@example.com" target="">das</a>?</p><p>Und <a href="https://luya.io" target="">extern</a>?</p><p>Und <a href="https://luya.io" target="_blank">Targets</a>?</p><p></p><iframe width="560" height="315" src="https://www.youtube.com/watch?v=Ceo8E40vdiI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
        $this->assertSame($expected, $content);
    }
}