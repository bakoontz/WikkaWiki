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
require_once('test/test.config.php');
require_once('libs/Wakka.class.php');
require_once('version.php');


class ReadMeTest extends PHPUnit_Framework_TestCase {
    
    protected static $pdo;
    protected static $wakka;
    protected static $config;
 
    /**
     * Test Fixtures
     */
    public static function setUpBeforeClass() {
        global $wikkaTestConfig;
        self::$config = $wikkaTestConfig;
        
        # create db connection
        $host = sprintf('mysql:host=%s', self::$config['mysql_host']);
        self::$pdo = new PDO($host, self::$config['mysql_user'],
            self::$config['mysql_password']);

        # create database
        self::$pdo->exec(sprintf('CREATE DATABASE `%s`',
            self::$config['mysql_database']));
        self::$wakka = new Wakka(self::$config);
    }
 
    public static function tearDownAfterClass() {
        self::$wakka = NULL;
        
        # cleanup database
        self::$pdo->exec(sprintf('DROP DATABASE `%s`',
            self::$config['mysql_database']));
        self::$pdo = NULL;
    }
    
    
    /**
     * Tests
     */
    public function testNotWrittenYet() {
        # see http://phpunit.de/manual/current/en/incomplete-and-skipped-tests.html
        $this->markTestIncomplete('For future tests. Will report as incomplete.');
    }
    
    public function testWikkaPresence() {
        $this->assertInstanceOf('Wakka', self::$wakka);
    }
    
    public function testTruth() {
        $this->assertTrue(true);
    }
}