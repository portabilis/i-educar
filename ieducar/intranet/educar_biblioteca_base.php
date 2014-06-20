<?php
class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Biblioteca" );
		$this->processoAp = "591";
                $this->addEstilo( "localizacaoSistema" );
	}
}