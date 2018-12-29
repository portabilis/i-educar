CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;
COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';

CREATE EXTENSION IF NOT EXISTS pg_stat_statements WITH SCHEMA public;
COMMENT ON EXTENSION pg_stat_statements IS 'track execution statistics of all SQL statements executed';

CREATE EXTENSION IF NOT EXISTS unaccent WITH SCHEMA public;
COMMENT ON EXTENSION unaccent IS 'text search dictionary that removes accents';
