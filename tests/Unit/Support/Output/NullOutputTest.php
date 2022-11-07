<?php

namespace Tests\Unit\Support\Output;

use App\Contracts\Output;
use iEducar\Support\Output\NullOutput;
use Tests\TestCase;

class NullOutputTest extends TestCase
{
    public function testNullOutputImplementInterfaceOutPut()
    {
        $this->assertInstanceOf(Output::class, new NullOutput());
    }

    public function testMethodProgressAdvanceWillReturnVoid()
    {
        $this->assertNull((new NullOutput())->progressAdvance());
    }

    public function testMethodInfoWillReturnVoid()
    {
        $this->assertNull((new NullOutput())->info('message'));
    }

    public function testMethodProgressStartWillReturnVoid()
    {
        $this->assertNull((new NullOutput())->progressStart('max'));
    }

    public function testMethodProgressFinishWillReturnVoid()
    {
        $this->assertNull((new NullOutput())->progressFinish());
    }
}
