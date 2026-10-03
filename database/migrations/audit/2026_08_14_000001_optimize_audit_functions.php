<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Substitui as funções de auditoria por versões mais rápidas e com menos ruído.
     */
    public function up(): void
    {
        DB::unprepared($this->getSqlForAuditEnabledFunction());
        DB::unprepared($this->getSqlForAuditContextFunction());
        DB::unprepared($this->getSqlForAuditFunction());
    }

    public function down(): void
    {
        DB::unprepared($this->getSqlForPreviousAuditEnabledFunction());
        DB::unprepared($this->getSqlForPreviousAuditContextFunction());
        DB::unprepared($this->getSqlForPreviousAuditFunction());
    }

    private function getSqlForAuditEnabledFunction(): string
    {
        return <<<'SQL'
create or replace function public.audit_enabled()
returns boolean
language sql
stable
as $function$
    select coalesce(lower(current_setting('audit.enabled', true)) not in ('false', 'off', '0', 'f', 'no'), true);
$function$;
SQL;
    }

    private function getSqlForAuditContextFunction(): string
    {
        return <<<'SQL'
create or replace function public.audit_context()
returns json
language plpgsql
stable
as $function$
declare
    v text := current_setting('audit.context', true);
begin
    if v is null or v = '' then
        return json_build_object('user_id', 0, 'user_name', session_user);
    end if;

    begin
        return v::json;
    exception when others then
        return json_build_object('user_id', 0, 'user_name', session_user);
    end;
end;
$function$;
SQL;
    }

    private function getSqlForAuditFunction(): string
    {
        return <<<'SQL'
create or replace function public.audit()
returns trigger as
$function$
declare
    carimbos constant text[] := array['updated_at', 'data_rev', 'idpes_rev', 'updated_by', 'last_used_at'];

    j_old jsonb;
    j_new jsonb;
begin
    if not public.audit_enabled() then
        return null;
    end if;

    if TG_OP = 'UPDATE' then
        if old::text is not distinct from new::text then
            return null;
        end if;

        j_old := to_jsonb(old.*);

        if j_old ?| carimbos then
            j_new := to_jsonb(new.*);

            if j_old - carimbos = j_new - carimbos then
                return null;
            end if;
        end if;
    end if;

    insert into public.ieducar_audit ("date", "schema", "table", "context", "before", "after")
    values (
        now(), TG_TABLE_SCHEMA, TG_TABLE_NAME, public.audit_context(),
        case when TG_OP in ('UPDATE', 'DELETE') then to_json(old.*) end,
        case when TG_OP in ('INSERT', 'UPDATE') then to_json(new.*) end
    );

    return null;
end;
$function$
language plpgsql;
SQL;
    }

    private function getSqlForPreviousAuditEnabledFunction(): string
    {
        return <<<'SQL'
create or replace function public.audit_enabled()
returns boolean as
$function$
begin
	begin
		return current_setting('audit.enabled');
	exception when others then
		return true;
	end;
end;
$function$
language plpgsql;
SQL;
    }

    private function getSqlForPreviousAuditContextFunction(): string
    {
        return <<<'SQL'
create or replace function public.audit_context()
returns json as
$function$
begin
	begin
		return current_setting('audit.context');
	exception when others then
		return json_build_object('user_id', 0, 'user_name', session_user);
	end;
end;
$function$
language plpgsql;
SQL;
    }

    private function getSqlForPreviousAuditFunction(): string
    {
        return <<<'SQL'
create or replace function public.audit()
returns trigger as
$function$
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
$function$
language plpgsql;
SQL;
    }
};
