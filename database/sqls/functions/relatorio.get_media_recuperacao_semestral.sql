CREATE OR REPLACE FUNCTION relatorio.get_media_recuperacao_semestral(matricula integer, componente integer) RETURNS numeric
    LANGUAGE plpgsql
AS $$
DECLARE
    nota_etapa1 numeric;
    nota_etapa2 numeric;
    nota_etapa3 numeric;
    nota_etapa4 numeric;
    nota_recuperacao1 numeric;
    nota_recuperacao2 numeric;
    nota_exame_final numeric;
    media_semestre1 numeric;
    media_semestre2 numeric;

    resultado numeric;
    media_final numeric;

    nota_aluno integer;
BEGIN

    nota_aluno := (SELECT id FROM modules.nota_aluno WHERE matricula_id = matricula);

    nota_etapa1 := (SELECT nota FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = '1');
    nota_etapa2 := (SELECT nota FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = '2');
    nota_etapa3 := (SELECT nota FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = '3');
    nota_etapa4 := (SELECT nota FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = '4');

    nota_recuperacao1 := (SELECT nota_recuperacao_especifica::numeric FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = '2');
    nota_recuperacao2 := (SELECT nota_recuperacao_especifica::numeric FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = '4');

    nota_exame_final := (SELECT nota FROM modules.nota_componente_curricular WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente AND etapa = 'Rc');
    media_final := (SELECT media FROM modules.nota_componente_curricular_media WHERE nota_aluno_id = nota_aluno AND componente_curricular_id = componente);

    IF nota_etapa2 > 0 THEN
        media_semestre1 := (nota_etapa1 + nota_etapa2) / 2;
    ELSE
        media_semestre1 := nota_etapa1;
    END IF;

    IF nota_etapa4 > 0 THEN
        media_semestre2 := (nota_etapa3 + nota_etapa4) / 2;
    ELSE
        media_semestre2 := nota_etapa3;
    END IF;

    IF nota_recuperacao1 >= media_semestre1 THEN
        media_semestre1 := (media_semestre1 + nota_recuperacao1) / 2;
    END IF;

    IF nota_recuperacao2 >= media_semestre2 THEN
        media_semestre2 := (media_semestre2 + nota_recuperacao2) / 2;
    END IF;

    IF nota_exame_final > 0 THEN
        resultado := media_final;
    ELSEIF media_semestre2 > 0 THEN
        resultado := (media_semestre1 + media_semestre2) / 2;
    ELSE
        resultado := media_semestre1;
    END IF;

    RETURN trunc(resultado,1);

END; $$;
