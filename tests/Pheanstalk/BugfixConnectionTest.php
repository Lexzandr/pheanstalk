<?php

namespace Pheanstalk;

use PHPUnit\Framework\TestCase;

/**
 * Tests for reported/discovered issues & bugs which don't fall into
 * an existing category of tests.
 * Relies on a running beanstalkd server.
 *
 */
class BugfixConnectionTest extends TestCase
{
    /**
     * Issue: NativeSocket's read() doesn't work with jobs larger than 8192 bytes.
     *
     * @see http://github.com/pda/pheanstalk/issues/4
     *
     * PHP 5.2.10-2ubuntu6.4 reads nearly double that on the first fread().
     * This is probably due to a prior call to fgets() pre-filling the read buffer.
     */
    public function testIssue4ReadingOver8192Bytes()
    {
        $length = 8192 * 3;

        $pheanstalk = $this->createPheanstalk();
        $pheanstalk->put(str_repeat('.', $length));
        $job = $pheanstalk->peekReady();
        $this->assertEquals(strlen($job->getData()), $length, 'data length: %s');
    }

    /**
     * Issue: NativeSocket's read() cannot read all the bytes we want at once.
     *
     * @see http://github.com/pda/pheanstalk/issues/issue/16
     *
     * @author SlNPacifist
     */
    public function testIssue4ReadingDifferentNumberOfBytes()
    {
        $pheanstalk = $this->createPheanstalk();
        $maxLength = 10000;
        $delta = str_repeat('a', 1000);
        // Let's repeat 20 times to make problem more obvious on Linux OS (it happens randomly)
        for ($i = 0; $i < 16; $i++) {
            for ($message = $delta; strlen($message) < $maxLength; $message .= $delta) {
                $pheanstalk->put($message);
                $job = $pheanstalk->peekReady();
                $pheanstalk->delete($job);
                $this->assertEquals($job->getData(), $message);
            }
        }
    }

    // ----------------------------------------
    // private

    private function createPheanstalk()
    {
        $pheanstalk = Pheanstalk::create(SERVER_HOST);
        $tube = preg_replace('#[^a-z]#', '', strtolower(__CLASS__));

        $pheanstalk
            ->useTube($tube)
            ->watch($tube)
            ->ignore('default');

        while (null !== $job = $pheanstalk->peekDelayed()) {
            $pheanstalk->delete($job);
        }
        while (null !== $job = $pheanstalk->peekReady()) {
            $pheanstalk->delete($job);
        }
        return $pheanstalk;
    }
}
