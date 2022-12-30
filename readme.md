# Teste Back-end

## Para rodar a aplicação faça os seguintes passos:

###  Comandos para inicialização:
```code
composer install
```
```code
php artisan serve
```
### 1.1 - Endpoint API:
```code
http://localhost:8000/api/usuario
```
* API resource do usuario, funções disponiveis por essa url:
     
        1 - INDEX, STORE, DESTROY e SHOW;
        
* INDEX - Listagem de todos os Usuários;
            
 * ####      Campos de filtro disponivel: 
                1.1 - CPF ou CNPJ;
                1.2 - Nome da pessoa;

* STORE - Metodo para armazenar usuarios novos;
 * ####      Campos Obrigatórios: 
                1.1 - CPF ou CNPJ;
                1.2 - Nome da pessoa;
                1.3 - Senha do Usuário;

* DESTROY - Metodo para apagar um usuario específico;
 * ####      Campos Obrigatórios: 
                1.1 - ID do Usuário;

* SHOW - Metodo para visualizar um usuario específico;
 * ####      Campos Obrigatórios: 
                1.1 - ID do Usuário;
```code
http://localhost:8000/api/produtor:
```    
* API resource do produtor. Funções disponiveis por essa url:
     
        1 - INDEX, STORE, DESTROY e SHOW;

* INDEX - Listagem de todos os Produtores;
            
 * ####      Campos de filtro disponivel: 
                1.1 - CPF ou CNPJ;
                1.2 - Nome da pessoa;

* STORE - Metodo para armazenar produtores novos; 
 
 * ####      Campos Obrigatórios: 
                1.1 - CPF ou CNPJ;
                1.2 - Nome da pessoa;

* DESTROY - Metodo para apagar um produtor específico;
 
 * ####      Campos Obrigatórios: 
                1.1 - ID do produtor;

* SHOW - Metodo para visualizar um produtor específico;
 
 * ####      Campos Obrigatórios: 
                1.1 - ID do produtor;
```code
http://localhost:8000/api/propriedade:
```
* API resource das propriedades. Funções disponiveis por essa url:
     
        1 - INDEX, STORE, DESTROY e SHOW;
        
* INDEX - Listagem de todos as Propriedade;
            
 * ####      Campos de filtro disponivel: 
                1.1 - Nome da Propriedade;

* STORE - Metodo para armazenar propriedades novas; 
  * ####     Campos Obrigatórios: 
                1.1 - Nome da Propriedade;
                1.2 - Cadastro Rural;

* DESTROY - Metodo para apagar uma propriedade específica;
  * ####     Campos Obrigatórios: 
                1.1 - ID da propriedade;

* SHOW - Metodo para visualizar uma propriedade específico;
 * ####      Campos Obrigatórios: 
                1.1 - ID do propriedade;
```code
http://localhost:8000/api/login:
```
* Endpoint do tipo POST. Funções disponiveis por essa url:
        
        1 - Login;

* LOGIN - Metodo para autenticar um usuário;

 * ####      Campos Obrigatórios: 
                1.1 - Nome do Usuário;
                1.2 - Senha do Usuário;

