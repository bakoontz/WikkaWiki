<?php
/**
 * DevTest.php
 * 
 * A simple test to verify PhpUnit is configured properly and demonstrate basic
 * test practices for WikkaWiki project. PhpUnit must be installed.
 *
 * Usage (run from WikkaWiki root dir):
 * > phpunit test
 *
 * For information on installing PhpUnit:
 * - http://phpunit.de/manual/3.7/en/installation.html
 * - http://book.cakephp.org/2.0/en/development/testing.html#installing-phpunit
 */
require_once('libs/Wakka.class.php');


class ReadMeTest extends PHPUnit_Framework_TestCase
{
    protected static $wakka;
 
    public static function setUpBeforeClass() {
        self::$wakka = new Wakka(array());
    }
 
    public static function tearDownAfterClass() {
        self::$wakka = NULL;
    }
    
    public function testWikkaPresence() {
        $this->assertInstanceOf('Wakka', self::$wakka);
    }
    
    public function testTruth() {
        $this->assertTrue(true);
    }
}