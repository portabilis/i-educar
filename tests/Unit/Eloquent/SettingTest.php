<?php

namespace Tests\Unit\Eloquent;

use App\Setting;
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
        $settingTrueString = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => 'true',
        ]);

        $settingOneString = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => '1',
        ]);

        $settingOne = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => 1,
        ]);

        $settingTrue = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => true,
        ]);

        $this->assertTrue($settingTrueString->value);
        $this->assertTrue($settingOneString->value);
        $this->assertTrue($settingOne->value);
        $this->assertTrue($settingTrue->value);

        $settingFalseString = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => 'false',
        ]);

        $settingFalseZeroString = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => '0',
        ]);

        $settingFalseZero = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => 0,
        ]);

        $settingFalse = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => false,
        ]);

        $settingFalseEmptyString = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => '',
        ]);

        $settingFalseNull = factory(Setting::class)->create([
            'type' => 'boolean',
            'value' => null,
        ]);

        $settingFalseNullString = factory(Setting::class)->create([
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
