# Sistema de upload do i-Educar

## Tipos de upload para o i-Educar

* Local
* Nuvem utilizando o S3 AWS
* Nuvem utilizando o MinIO

## Pré-requisitos

* Leia a documentação oficial do Laravel: https://laravel.com/docs/5.8/filesystem
* Caso use o S3 AWS, tenha em mãos os seguinte dados:
  * Chave de acesso: Access Key
  * Segredo da chave de acesso: Secret Access Key
  * Região: Region
  * Bucket: Bucket Name
  * Url: Endereço
* Caso use o Minio, tenha em mão os seguintes dados:
  * Chave de acesso: Access Key
  * Segredo da chave de acesso: Secret Access Key
  * Bucket: Bucket Name
  * Minio URL: Endereço
* Caso use o Storage Local, execute o seguinte comando:
  * Usando docker: ``docker-compose exec php artisan storage:link``
  * Usando o release: ``php artisan storage:link``
  * E tenha certeza que a pasta storage está com as permissões corretas de escrita

## Configurando o upload

### Nuvem utilizando o S3 AWS

Altere essas linhas no arquivo .env colocando suas credenciais do serviço S3 da AWS.
```
FILESYSTEM_DRIVER=s3

# S3 config
AWS_URL = 
AWS_KEY=
AWS_SECRET=
AWS_DEFAULT_REGION=
AWS_BUCKET=
```

### Nuvem utilizando o Min.IO

Altere essas linhas no arquivo ``.env`` colocando suas credenciais do serviço Min.IO.
```
FILESYSTEM_DRIVER=minio

# MINIO config
MINIO_ENDPOINT = 
AWS_KEY=
AWS_SECRET=
AWS_DEFAULT_REGION=
AWS_BUCKET=
```

### Local

Altere essas linhas no arquivo ``.env`` colocando suas credenciais do serviço Min.IO.
```
FILESYSTEM_DRIVER=local
```

Todos os arquivos armazenados pelo modo local fica na pasta ``public/storage``.

