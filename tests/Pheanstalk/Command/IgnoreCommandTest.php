<?php

declare(strict_types=1);

namespace Pheanstalk\Tests\Command;

use Pheanstalk\Command\IgnoreCommand;
use Pheanstalk\Exception\NotIgnoredException;
use Pheanstalk\RawResponse;
use Pheanstalk\ResponseType;
use Pheanstalk\TubeName;
use PHPUnit\Framework\Assert;

/**
 * @covers \Pheanstalk\Command\IgnoreCommand
 */
class IgnoreCommandTest extends TubeCommandTest
{
    public function testInterpretWatching(): void
    {
        $command = $this->getSubject();
        $watching = $command->interpret(new RawResponse(ResponseType::Watching, "5"));
        Assert::assertSame(5, $watching);
    }

    public function testInterpretNotIgnored(): void
    {
        $command = $this->getSubject();
        $this->expectException(NotIgnoredException::class);
        $command->interpret(new RawResponse(ResponseType::NotIgnored));
    }

    protected function getSupportedResponses(): array
    {
        return [ResponseType::NotIgnored, ResponseType::Watching];
    }

    protected function getSubject(TubeName $tube = null): IgnoreCommand
    {
        return new IgnoreCommand($tube ?? new TubeName("default"));
    }
}
