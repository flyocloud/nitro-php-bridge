<?php

namespace Flyo\Bridge;

use Nadar\ProseMirror\Node;
use Nadar\ProseMirror\NodeType;
use Nadar\ProseMirror\Parser;

/**
 * Quick Parser for WYSIWYG
 *
 * ```php
 * $html = Wysiwyg::render($json);
 * ```
 *
 * or for further customization:
 *
 * ```php
 * $html = Wyswyg::render($json, function(Wysiwyg $parser) {
 *    $parser
 *      ->replaceNode(NodeType::image, fn (Node $node) => "<img class=\"w-full\" src=\"{$node->getAttr('src')}\" alt=\"{$node->getAttr('alt')}\">")
 *      ->addNode('box', fn (Node $node) => "<span class=\"font-semibold mx-6 my-3 text-left flex-auto\">".nl2br($node->getAttr('text'))."</span>");
 * });
 * ```
 */
class Wysiwyg extends Parser
{
    public static function render(mixed $json, ?callable $parser = null): string
    {
        $object = new self();

        $object->replaceNode(NodeType::image, function (Node $node) {
            $src = $node->getAttr('src');
            if (is_array($src) && isset($src['source'])) {
                $src = $src['source'];
            } elseif (is_object($src) && isset($src->source)) {
                $src = $src->source;
            }
            return '<img src="' . $src . '" alt="' . $node->getAttr('alt') . '" title="' . $node->getAttr('title') . '" />';
        });

        if (is_object($json)) {
            $json = json_decode(json_encode($json, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        }

        if (is_callable($parser)) {
            call_user_func($parser, $object);
        }

        return $object->toHtml($json);

    }
}
