<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AuditFunctions extends Migration
{
    /**
     * Return SQL to drop a function.
     *
     * @param string $function
     *
     * @return string
     */
    private function getSqlForDropFunction($function)
    {
        return "drop function if exists {$function}();";
    }

    /**
     * Return SQL to create audit context function.
     *
     * @return string
     */
    private function getSqlForAuditContextFunction()
    {
        return <<<SQL
create function audit_context()
returns json as
\$function$
begin
	begin
		return current_setting('audit.context');
	exception when others then
		return json_build_object('user_id', 0, 'user_name', session_user);
	end;
end;
\$function$
language plpgsql;
SQL;
    }

    /**
     * Return SQL to create audit enabled function.
     *
     * @return string
     */
    private function getSqlForAuditEnabledFunction()
    {
        return <<<SQL
create function audit_enabled()
returns boolean as
\$function$
begin
	begin
		return current_setting('audit.enabled');
	exception when others then
		return true;
	end;
end;
\$function$
language plpgsql;
SQL;
    }

    /**
     * Return SQL to create audit function.
     *
     * @return string
     */
    private function getSqlForAuditFunction()
    {
        return <<<SQL
create function public.audit()
returns trigger as
\$function$
begin
	if (audit_enabled() = false) then
		return null;
	end if;

	if (TG_OP = 'DELETE') then
		insert into ieducar_audit ("date", "schema", "table", "context", "before", "after")
		values (now(), TG_TABLE_SCHEMA::text, TG_TABLE_NAME::text, audit_context(), to_json(old.*), null);

		return old;
	end if;

	if (TG_OP = 'UPDATE') then
		insert into ieducar_audit ("date", "schema", "table", "context", "before", "after")
		values (now(), TG_TABLE_SCHEMA::text, TG_TABLE_NAME::text, audit_context(), to_json(old.*), to_json(new.*));

		return old;
	end if;

	if (TG_OP = 'INSERT') then
		insert into ieducar_audit ("date", "schema", "table", "context", "before", "after")
		values (now(), TG_TABLE_SCHEMA::text, TG_TABLE_NAME::text, audit_context(), null, to_json(new.*));

		return old;
	end if;

	return null;
end;
\$function$
language plpgsql;
SQL;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared($this->getSqlForDropFunction('audit'));
        DB::unprepared($this->getSqlForDropFunction('audit_context'));
        DB::unprepared($this->getSqlForDropFunction('audit_enabled'));
        DB::unprepared($this->getSqlForAuditContextFunction());
        DB::unprepared($this->getSqlForAuditEnabledFunction());
        DB::unprepared($this->getSqlForAuditFunction());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared($this->getSqlForDropFunction('audit'));
        DB::unprepared($this->getSqlForDropFunction('audit_context'));
        DB::unprepared($this->getSqlForDropFunction('audit_enabled'));
    }
}
