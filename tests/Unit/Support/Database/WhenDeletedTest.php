<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->instance = new class
    {
        use WhenDeleted;
    };

    /**
     * @return void
     */
    $this->getDropTriggerSql = function () {
        $expected = 'DROP TRIGGER IF EXISTS trigger_when_deleted_public_table on public.table;';

        $this->assertEquals($expected, $this->instance->getTriggerName('public.table'));
    };
});

test('trait', function () {
    $from = 'schema.table';
    $columns = ['id', 'name'];

    $expectedFunctionName = 'when_deleted_schema_table';
    $this->assertEquals($expectedFunctionName, $this->instance->getFunctionName($from));

    $expectedTriggerName = 'trigger_when_deleted_schema_table';
    $this->assertEquals($expectedTriggerName, $this->instance->getTriggerName($from));

    $expectedColumns = 'id, name, updated_at, deleted_at';
    $this->assertEquals($expectedColumns, $this->instance->getColumns($columns));

    $expectedValueS = 'OLD.id, OLD.name, NOW(), NOW()';
    $this->assertEquals($expectedValueS, $this->instance->getColumnsValues($columns));
});

test('create function sql', function () {
    $expected = '
            CREATE FUNCTION when_deleted_some_table() 
            RETURNS TRIGGER 
            LANGUAGE plpgsql AS
            $$
            BEGIN
                INSERT INTO some_other_table (id, name, updated_at, deleted_at) VALUES (OLD.id, OLD.name, NOW(), NOW());
                RETURN OLD;
            END;
            $$;
        ';

    $this->assertEquals(
        $expected,
        $this->instance->getCreateFunctionSql('some_table', 'some_other_table', ['id', 'name'])
    );
});

test('drop function sql', function () {
    $expected = 'DROP FUNCTION IF EXISTS when_deleted_other_table();';

    $this->assertEquals($expected, $this->instance->getDropFunctionSql('other_table'));
});

test('create trigger sql', function () {
    $expected = '
            CREATE TRIGGER trigger_when_deleted_table AFTER DELETE
            ON table FOR EACH ROW
            EXECUTE PROCEDURE when_deleted_table();
        ';

    $this->assertEquals($expected, $this->instance->getCreateTriggerSql('table'));
});

test('create', function () {
    $expectedFunction = '
            CREATE FUNCTION when_deleted_some_table() 
            RETURNS TRIGGER 
            LANGUAGE plpgsql AS
            $$
            BEGIN
                INSERT INTO other_table (id, name, updated_at, deleted_at) VALUES (OLD.id, OLD.name, NOW(), NOW());
                RETURN OLD;
            END;
            $$;
        ';

    $expectedTrigger = '
            CREATE TRIGGER trigger_when_deleted_some_table AFTER DELETE
            ON some_table FOR EACH ROW
            EXECUTE PROCEDURE when_deleted_some_table();
        ';

    DB::shouldReceive('unprepared')
        ->once()
        ->with($expectedFunction);

    DB::shouldReceive('unprepared')
        ->once()
        ->with($expectedTrigger);

    $this->instance->whenDeletedMoveTo('some_table', 'other_table', ['id', 'name']);
});

test('drop', function () {
    $expectedTrigger = 'DROP TRIGGER IF EXISTS trigger_when_deleted_some_table on some_table;';
    $expectedFunction = 'DROP FUNCTION IF EXISTS when_deleted_some_table();';

    DB::shouldReceive('unprepared')
        ->once()
        ->with($expectedTrigger);

    DB::shouldReceive('unprepared')
        ->once()
        ->with($expectedFunction);

    $this->instance->dropTriggerWhenDeleted('some_table');
});
