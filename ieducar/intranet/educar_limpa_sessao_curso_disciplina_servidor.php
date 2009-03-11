<?

		@session_start();
		unset($_SESSION['cursos_disciplina']);
		unset($_SESSION['cursos_servidor']);
		unset($_SESSION['cod_servidor']);
		@session_write_close();
		echo "";

?>