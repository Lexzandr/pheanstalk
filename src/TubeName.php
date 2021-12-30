<?php

declare(strict_types=1);

namespace Pheanstalk;

use Stringable;

class TubeName implements Stringable
{
    private const NAME_REGEX = '~^[A-Za-z0-9+/;.$_()][A-Za-z0-9\-+/;.$_()]*$~';

    public readonly string $value;

    public function __construct(string $value)
    {
        if (mb_strlen($value, '8bit') > 200) {
            throw new \InvalidArgumentException("Tube name too long");
        } elseif (preg_match(self::NAME_REGEX, $value) === 0) {
            throw new \InvalidArgumentException("Invalid tube name format");
        }
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
