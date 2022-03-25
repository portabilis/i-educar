<?

class ListaAlunoTurmaController extends ApiCoreController
{
    public function pegarlista()
    {
        $cod_turma = $this->getRequest()->cod_turma;

        if(is_numeric($cod_turma)){
            $obj = new clsPmieducarMatriculaTurma();
            $lista = $obj->listaPorSequencial($cod_turma);

            return ['lista' => $lista];

        }

        return[];
    }
   public function gerar()
   {
       if($this->isRequestFor('post', 'lista')){
           $this->appendResponse($this->pegarlista());
       }
   } 
}