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
        $settingTrueString = Setting::factory()->create([
            'type' => 'boolean',
            'value' => 'true',
        ]);

        $settingOneString = Setting::factory()->create([
            'type' => 'boolean',
            'value' => '1',
        ]);

        $settingOne = Setting::factory()->create([
            'type' => 'boolean',
            'value' => 1,
        ]);

        $settingTrue = Setting::factory()->create([
            'type' => 'boolean',
            'value' => true,
        ]);

        $this->assertTrue($settingTrueString->value);
        $this->assertTrue($settingOneString->value);
        $this->assertTrue($settingOne->value);
        $this->assertTrue($settingTrue->value);

        $settingFalseString = Setting::factory()->create([
            'type' => 'boolean',
            'value' => 'false',
        ]);

        $settingFalseZeroString = Setting::factory()->create([
            'type' => 'boolean',
            'value' => '0',
        ]);

        $settingFalseZero = Setting::factory()->create([
            'type' => 'boolean',
            'value' => 0,
        ]);

        $settingFalse = Setting::factory()->create([
            'type' => 'boolean',
            'value' => false,
        ]);

        $settingFalseEmptyString = Setting::factory()->create([
            'type' => 'boolean',
            'value' => '',
        ]);

        $settingFalseNull = Setting::factory()->create([
            'type' => 'boolean',
            'value' => null,
        ]);

        $settingFalseNullString = Setting::factory()->create([
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
