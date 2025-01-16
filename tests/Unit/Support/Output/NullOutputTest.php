<?php

namespace Tests\Unit\Support\Output;

use App\Contracts\Output;
use iEducar\Support\Output\NullOutput;
use Tests\TestCase;

class NullOutputTest extends TestCase
{
    public function test_null_output_implement_interface_out_put()
    {
        $this->assertInstanceOf(Output::class, new NullOutput);
    }

    public function test_method_progress_advance_will_return_void()
    {
        $this->assertNull((new NullOutput)->progressAdvance());
    }

    public function test_method_info_will_return_void()
    {
        $this->assertNull((new NullOutput)->info('message'));
    }

    public function test_method_progress_start_will_return_void()
    {
        $this->assertNull((new NullOutput)->progressStart('max'));
    }

    public function test_method_progress_finish_will_return_void()
    {
        $this->assertNull((new NullOutput)->progressFinish());
    }
}
