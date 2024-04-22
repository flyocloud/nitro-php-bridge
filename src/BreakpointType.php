<?php

namespace Flyo\Bridge;

enum BreakpointType
{
    case PX_OR_LESS;

    case PX_OR_MORE;

    public function srcset(int $screenSize, string $src): string
    {
        return "{$src} {$screenSize}w";
    }

    public function size(int $screenSize, int $maxWidth): string
    {
        $vw = floor(($screenSize / $maxWidth) * 100);
        return "({$this->width()}: {$screenSize}px) {$vw}vw";
    }

    public function width(): string
    {
        return match($this) {
            self::PX_OR_LESS => 'max-width',
            self::PX_OR_MORE => 'min-width',
        };
    }
}
