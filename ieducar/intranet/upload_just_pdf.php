<?PHP

  require_once 'file_check_just_pdf.php';

  if ($_FILES){
    foreach ($_FILES as $file) {
      if (!empty($file["name"])){
        $objFile = new FileController($file);
        if ($objFile->validateFile()){
          $caminho = $objFile->sendFile();
          if ($caminho!=''){
            $data = array('file_url' => $caminho);
          }else{
            $data = array('error' => $objFile->getErrorMessage());
          }
        } else {
          $data = array('error' => $objFile->getErrorMessage());
        }
      }else{
        $data = array('error' => 'Nenhum arquivo enviado.');
      }
    }
  }else{
    $data = array('error' => 'Arquivo invÃ¡lido.');
  }

  echo json_encode($data);
?>