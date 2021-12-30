<?php

declare(strict_types=1);

namespace Pheanstalk;

use ArrayIterator;
use Traversable;

/**
 * @codeCoverageIgnore Remove this annotation if any significant functionality gets added to this class.
 * @implements \IteratorAggregate<int, TubeName>
 */
class TubeList implements \IteratorAggregate
{
    /**
     * @var list<TubeName>
     */
    private readonly array $tubes;
    public function __construct(TubeName ...$tubes)
    {
        $this->tubes = array_values($tubes);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tubes);
    }
}
