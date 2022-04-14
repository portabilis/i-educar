<?php

namespace Tests\Unit\Eloquent;

use App\Setting;
use Database\Factories\SettingFactory;
use Tests\EloquentTestCase;

class SettingTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Setting::class;
    }

    /**
     * @return void
     */
    public function testBoolean()
    {
        $settingTrueString = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => 'true',
        ]);

        $settingOneString = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => '1',
        ]);

        $settingOne = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => 1,
        ]);

        $settingTrue = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => true,
        ]);

        $this->assertTrue($settingTrueString->value);
        $this->assertTrue($settingOneString->value);
        $this->assertTrue($settingOne->value);
        $this->assertTrue($settingTrue->value);

        $settingFalseString = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => 'false',
        ]);

        $settingFalseZeroString = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => '0',
        ]);

        $settingFalseZero = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => 0,
        ]);

        $settingFalse = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => false,
        ]);

        $settingFalseEmptyString = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => '',
        ]);

        $settingFalseNull = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => null,
        ]);

        $settingFalseNullString = SettingFactory::new()->create([
            'type' => 'boolean',
            'value' => 'null',
        ]);

        $this->assertFalse($settingFalseString->value);
        $this->assertFalse($settingFalseZeroString->value);
        $this->assertFalse($settingFalseZero->value);
        $this->assertFalse($settingFalse->value);
        $this->assertFalse($settingFalseEmptyString->value);
        $this->assertFalse($settingFalseNull->value);
        $this->assertFalse($settingFalseNullString->value);
    }
}
