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

    public function size(int $screenSize): string
    {
        $width = $this->width();
        return "($width: {$screenSize}px) {$screenSize}px";
    }

    public function width(): string
    {
        return match($this) {
            self::PX_OR_LESS => 'max-width',
            self::PX_OR_MORE => 'min-width',
        };
    }
}
