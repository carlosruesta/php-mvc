## Instalar o projeto

1.- Execute composer install para instalar todas as dependências necessárias

2.- Crie o banco de dados executando o comando:
```
php vendor/bin/doctrine orm:schema-tool:create
```

3.- Execute o seguinte comando para criar um usuário com e-mail *email@example.com* e senha *123456*

```
php vendor/bin/doctrine dbal:run-sql "INSERT INTO usuarios (email, senha) VALUES ('email@example.com', '\$argon2i\$v=19\$m=65536,t=4,p=1\$WHpBb1FzTDVpTmQubU55bA\$jtZiWSSbmw1Ru4tYEq1SzShrMu0ap2PjblRQRubNPgo');"
```

4 Inicialize o projeto em um servidor web:

```
php -S 0.0.0.0:8080 -t public
```

5.- Testar o projeto no navegador na seguinte URL:

```
http://localhost:8080/login
```