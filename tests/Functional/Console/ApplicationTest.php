<?php

namespace Devmachine\MongoImport\Tests\Functional\Console;

use Devmachine\MongoImport\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @group functional
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_imports_extended_json_file()
    {
        $application = new Application();
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run(['file' => __DIR__.'/../../fixtures/employees.json'], [
            'drop' => true,
            'db'   => 'test',
        ]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('Docs inserted: 10', trim($tester->getDisplay()));
    }

    /**
     * @test
     */
    public function it_fails_on_missing_file()
    {
        $application = new Application();
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run(['file' => '__missing__'], [
            'drop' => true,
            'db'   => 'test',
        ]);

        $this->assertSame(1, $tester->getStatusCode());
    }
}
