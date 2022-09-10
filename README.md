SET UP
- Docker
- Postman

COMMANDS
- bash run.sh: vai compilar o cod
- http://adimin.localhost:8080/ (se retornar falso deu certo)

- docker ps (se o container do mysql estiver reiniciando é preciso arrumar. sessão DOCKER: PROBLEMA MYSQL)

- docker compose exec php80-service bash
- php bin/console doctrine:database:create (se der erro, alterar a porta na linha 45 do arquivo docker-compose.yml para a mesma que configurou o mysql)
- php bin/console make:migration
- php bin/console doctrine:migration:migrate

DOCKER: PROBLEMA MYSQL
- docker kill {codigo container mysql}
- docker images purge
- docker volume prune
- docker ps (conferir se o mysql foi apagado)
> configurar outro container pro mysql
- docker pull mysql
- docker run --name my-mysql -p 4306:3306 -e MYSQL_ROOT_PASSWORD=mypassword -d mysql (-p é a porta que vc esta habilitando para o mysql, tem que ser a mesma do arquivo docker-compose.yml)
- docker ps (conferir se o container do mysql está rodando normalmente)

POSTMAN
- **No banco de dados, criar (ou importar) uma dupla na tabela user.

> Admin.Area
	> Security
		. Login

- Mudar email para o cadastrado no bd e enviar.
- Apos o envio do email, será recebido um token.

> Admin.area

- bearer_token_admin: mudar init value e current value para igual o token.

>Admin.area
	>Departamento

- Apos salvar as alterações será possível criar um departamento
- Com o departamento criado será possível listar-los  

	>Flow
		. Create flow
			- Authorization

- É necessário mudar o type para Bearer Token

>Api.Area
	>Security
		. Create Token Access

- Mudar value para o identifier igual ao do departamento (. List Flow for Department).
