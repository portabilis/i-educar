CREATE OR REPLACE VIEW cadastro.v_fone_pessoa AS
 SELECT DISTINCT t.idpes,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))) AS ddd_1,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))) AS fone_1,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))) AS ddd_2,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))) AS fone_2,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))) AS ddd_mov,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))) AS fone_mov,
    ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))) AS ddd_fax,
    ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))) AS fone_fax
   FROM cadastro.fone_pessoa t
  ORDER BY t.idpes, ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (1)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (2)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (3)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.ddd
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes))), ( SELECT t1.fone
           FROM cadastro.fone_pessoa t1
          WHERE ((t1.tipo = (4)::numeric) AND (t.idpes = t1.idpes)));
