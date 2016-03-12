<?php
/**
 * Test: \Build.
 *
 * @testCase \BuildTest
 * @author Martin Jirásek <jertin@seznam.cz>
 * @package Kdyby\
 */

use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';
/**
 * @author Martin Jirásek <jertin@seznam.cz>
 */
class BuildTest extends Tester\TestCase
{
    public function setUp()
    {
    }

    /**
    public function testDatabase()
    {
        $ormGenerator = new \Zarganwar\NextrasOrmUtils\Generator(__DIR__ . "/target/database");
        $ormGenerator->parseSql(file_get_contents(__DIR__ . '/files/database.sql'));
    }*/

    public function testSimpleTable()
    {
        $expectedPath = __DIR__ . "/expected/simple-table";
        $targetPath = __DIR__ . "/target/simple-table";

        \Nette\Utils\FileSystem::delete($targetPath);

        $ormGenerator = new \Zarganwar\NextrasOrmUtils\Generator($targetPath);
        $ormGenerator->build(file_get_contents(__DIR__ . '/files/simple-table.sql'));

        Assert::matchFile("$expectedPath/Orm.php", file_get_contents("$targetPath/Orm.php"));
        Assert::matchFile("$expectedPath/Author/Author.php", file_get_contents("$targetPath/Author/Author.php"));
        Assert::matchFile("$expectedPath/Author/AuthorRepository.php", file_get_contents("$targetPath/Author/AuthorRepository.php"));
        Assert::matchFile("$expectedPath/Author/AuthorMapper.php", file_get_contents("$targetPath/Author/AuthorMapper.php"));
    }

    /*
    public function testFullDatabase()
    {
        $ormGenerator = new \Zarganwar\NextrasOrmUtils\Generator(__DIR__ . "/target/full-database");
        $ormGenerator->build(file_get_contents(__DIR__ . '/files/full-database.sql'));

    }*/
}
$test = new BuildTest();
$test->run();