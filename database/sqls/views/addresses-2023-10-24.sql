CREATE OR REPLACE VIEW public.addresses
AS
SELECT p.id,
       p.city_id,
       c.state_id,
       s.country_id,
       p.address,
       p.number,
       p.complement,
       p.neighborhood,
       p.postal_code,
       p.latitude,
       p.longitude,
       c.name         AS city,
       s.abbreviation AS state_abbreviation,
       s.name         AS state,
       cn.name        AS country,
       c.ibge_code    AS city_ibge_code,
       s.ibge_code    AS state_ibge_code,
       cn.ibge_code   AS country_ibge_code
FROM places p
         LEFT JOIN cities c ON c.id = p.city_id
         LEFT JOIN states s ON s.id = c.state_id
         LEFT JOIN countries cn ON cn.id = s.country_id;
