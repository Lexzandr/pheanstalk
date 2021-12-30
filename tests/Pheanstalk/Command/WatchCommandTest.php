<?php

declare(strict_types=1);

namespace Pheanstalk\Tests\Command;

use Pheanstalk\Command\WatchCommand;
use Pheanstalk\RawResponse;
use Pheanstalk\ResponseType;
use Pheanstalk\TubeName;
use PHPUnit\Framework\Assert;

/**
 * @covers \Pheanstalk\Command\WatchCommand
 */
class WatchCommandTest extends TubeCommandTest
{
    public function testInterpretWatching(): void
    {
        $command = $this->getSubject();
        $watching = $command->interpret(new RawResponse(ResponseType::Watching, "5"));
        Assert::assertSame(5, $watching);
    }

    protected function getSupportedResponses(): array
    {
        return [ResponseType::Watching];
    }

    protected function getSubject(TubeName $tube = null): WatchCommand
    {
        return new WatchCommand($tube ?? new TubeName("default"));
    }
}
