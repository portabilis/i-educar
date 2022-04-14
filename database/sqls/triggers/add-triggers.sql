CREATE TRIGGER trg_aft_documento AFTER INSERT OR UPDATE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_documento();
CREATE TRIGGER trg_aft_documento_provisorio AFTER INSERT OR UPDATE ON cadastro.documento FOR EACH ROW EXECUTE PROCEDURE cadastro.fcn_aft_documento_provisorio();
CREATE TRIGGER update_componente_curricular_turma_updated_at BEFORE UPDATE ON modules.componente_curricular_turma FOR EACH ROW EXECUTE PROCEDURE public.update_updated_at();
CREATE TRIGGER retira_data_cancel_matricula_trg AFTER UPDATE ON pmieducar.matricula FOR EACH ROW EXECUTE PROCEDURE public.retira_data_cancel_matricula_fun();
CREATE TRIGGER trigger_updated_at_matricula BEFORE UPDATE ON pmieducar.matricula FOR EACH ROW EXECUTE PROCEDURE pmieducar.updated_at_matricula();
CREATE TRIGGER trigger_updated_at_matricula_turma BEFORE UPDATE ON pmieducar.matricula_turma FOR EACH ROW EXECUTE PROCEDURE pmieducar.updated_at_matricula_turma();
CREATE TRIGGER update_escola_serie_disciplina_updated_at BEFORE UPDATE ON pmieducar.escola_serie_disciplina FOR EACH ROW EXECUTE PROCEDURE public.update_updated_at();
CREATE TRIGGER trigger_delete_matricula_turma AFTER DELETE ON pmieducar.matricula_turma FOR EACH ROW EXECUTE PROCEDURE pmieducar.delete_matricula_turma();
