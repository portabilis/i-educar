<?php

class FileStream
{
  /**
   * Instância da classe Mimetype
   *
   * @var Mimetype
   */
    protected $mimetype    = null;

    /**
     * Caminho do arquivo para stream
     *
     * @var string
     */
    protected $filepath    = null;

    /**
     * Array de diretórios permitidos para stream de arquivos.
     *
     * @var array
     */
    protected $allowedDirs = [];

    /**
     * Construtor.
     *
     * @param Mimetype $mimetype    Objeto Mimetype
     * @param array    $allowedDirs Diretórios permitidos para stream
     */
    public function __construct(Mimetype $mimetype, array $allowedDirs = [])
    {
        $this->mimetype = $mimetype;
        $this->setAllowedDirectories((array) $allowedDirs);
    }

    /**
     * Configura o nome do arquivo, verificando se o mesmo encontra-se em um
     * diretório de acesso permitido e se é legível.
     *
     * @param string $filepath O caminho completo ou relativo do arquivo
     *
     * @throws Exception
     */
    public function setFilepath($filepath)
    {
        $this->isReadable($filepath);
        $this->filepath = $filepath;
    }

    /**
     * Configura os diretórios permitidos para stream de arquivos.
     *
     * @param array $v
     */
    protected function setAllowedDirectories($v)
    {
        $this->allowedDirs = $v;
    }

    /**
     * Verifica se o arquivo é legível e se está em um diretório permitido
     * para stream de arquivos.
     *
     * @param string $filepath O caminho completo ou relativo ao arquivo
     *
     * @throws Exception
     */
    protected function isReadable($filepath)
    {
        $fileinfo = pathinfo($filepath);

        if (! $this->isDirectoryAllowed($fileinfo['dirname'])) {
            throw new Exception('Acesso ao diretório negado.');
        }

        if (! is_readable($filepath)) {
            throw new Exception('Arquivo não existe.');
        }
    }

    /**
     * Verifica se o diretório está na lista de diretórios permitidos para
     * stream de arquivos.
     *
     * @param string $directory
     *
     * @return bool Retorna TRUE se o diretório é permitido
     */
    public function isDirectoryAllowed($directory)
    {
        if (false === array_search($directory, $this->allowedDirs)) {
            return false;
        }

        return true;
    }

    /**
     * Faz o stream do arquivo.
     *
     * @throws Exception
     */
    public function streamFile()
    {
        $mimetype = $this->mimetype->getType($this->filepath);

        if (false === $mimetype) {
            throw new Exception('Extensão não suportada.');
        }

        // Headers para stream de arquivo
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimetype);
        header('Content-Disposition: attachment;');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->filepath));
        ob_clean();
        flush();

        // Lê o arquivo para stream buffer
        readfile($this->filepath);
    }
}
