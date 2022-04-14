<?
class ListaAlunoTurmaController extends ApiCoreController
{
    public function pegarlista()
    {
        $codTurma = $this->getRequest()->cod_turma;

        if(is_numeric($codTurma)){
            $obj = new clsPmieducarMatriculaTurma();
            $lista = $obj->listaPorSequencial($codTurma);

            return ['lista' => $lista];
        }

        return[];
    }
   public function gerar()
   {
       if($this->isRequestFor('get','lista')){
           $this->appendResponse($this->pegarlista());
       }
   } 
}