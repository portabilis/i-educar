<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use JasperPHP\JasperPHP;

class jasperphpTest extends \PHPUnit_Framework_TestCase
{
    protected $executable = "/../JasperStarter/bin/jasperstarter";

    public function testCreateInstance()
    {
        $obj = new JasperPHP;
        $this->assertTrue($obj instanceof JasperPHP);
    }

    public function testJava()
    {
        exec('which java', $output, $returnVar);
        if($returnVar != 0) {
            return $this->assertTrue(false);
        }
        $this->assertTrue(true);
    }

    public function testJasperStarter()
    {
        $executable = __DIR__ . $this->executable . " -h";

        exec($executable, $output, $returnVar);
        
        if($returnVar != 0) {
            return $this->assertTrue(false);
        }
        $this->assertTrue(true);
    }

    public function testCompileException()
    {
        $this->setExpectedException('Exception');
        JasperPHP::compile();
    }

    public function testProcessException()
    {
        $this->setExpectedException('Exception');
        JasperPHP::process();
    }

    public function testCompileOutput()
    {
        // TODO
        //    public static function compile($input_file, $output_file = false, $background = true, $redirect_output = true)
        //echo JasperPHP::compile("file.jrxml")->output() . PHP_EOL;

        //    public static function process($input_file, $output_file = false, $format = "pdf", $parameters = array(), $db_connection = array(), $background = true, $redirect_output = true)
        //echo JasperPHP::process("file.jasper")->output() . PHP_EOL;
    }
}