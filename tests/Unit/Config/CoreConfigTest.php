<?php

namespace Tests\Unit;

use iEducar\Modules\Config\CoreConfig;
use Tests\TestCase;

class CoreConfigTest extends TestCase
{
    public function testConfigHasValueFromArray()
    {
        $arr = [
            'app' => [
                'database' => [
                    'dbname' => 'ieducardb',
                    'username' => 'ieducaruser',
                    'password' => '12345678'
                ]
            ],
            'version' => 'Development'
        ];

        $config = new CoreConfig($arr);
        $this->assertEquals('ieducardb', $config->app->database->dbname);
        $this->assertEquals('Development', $config->version);
    }

    public function testHasOneItem()
    {
        $arr = [
            'app' => ['database' => '']
        ];

        $config = new CoreConfig($arr);
        $this->assertEquals(1, $config->count());
    }

    public function testHasTwoItems()
    {
        $arr = [
            'app' => ['database' => '', 'template' => ''],
            'php' => ''
        ];

        $config = new CoreConfig($arr);
        $this->assertEquals(2, $config->count());
        $this->assertEquals(2, $config->app->count());
    }

    /**
     * @expectedException Exception
     */
    public function testGetNotExistNotProvidingDefaultValue()
    {
        $arr = ['app' => ['database' => ['dbname' => 'ieducardb']]];

        $config = new CoreConfig($arr);
        $hostname = $config->get($config->app->database->hostname);
        $this->assertEquals($hostname, '127.0.0.1');
    }

    public function testGetNotExistProvidingDefaultValue()
    {
        $arr = ['app' => ['database' => ['dbname' => 'ieducardb']]];

        $config = new CoreConfig($arr);
        $hostname = $config->get($config->app->database->hostname, '127.0.0.1');
        $this->assertEquals($hostname, '127.0.0.1');
    }

    public function testGetExistProvidingDefaultValue()
    {
        $arr = ['app' => ['database' => ['dbname' => 'ieducardb']]];

        $config = new CoreConfig($arr);
        $hostname = $config->get($config->app->database->dbname, '127.0.0.1');
        $this->assertEquals($hostname, 'ieducardb');
    }

    public function testObjectIterates()
    {
        $arr = [
            'index1' => 1,
            'index2' => 2
        ];

        $config = new CoreConfig($arr);

        $this->assertEquals(1, $config->current());

        $config->next();
        $this->assertEquals(2, $config->current());

        foreach ($config as $key => $val) {
        }

        $config->rewind();
        $this->assertEquals(1, $config->current());
    }

    public function testTransformObjectInArray()
    {
        $arr = [6, 3, 3];

        $config = new CoreConfig($arr);

        $this->assertEquals($arr, $config->toArray());
    }
}