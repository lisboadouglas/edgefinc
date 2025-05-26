# Edge Finch Teste - Backend

API Laravel para consulta e simulação de ofertas de crédito integrada com a Gosat API


## 🚀 Começando

### Pré-requisitos

- PHP 8.2+
- Composer 2.6+
- MySQL 8.0+ ou PostgreSQL
- Extensões PHP: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML

### 🔧 Configuração

1. **Clonar repositório**
```bash
git clone git@github.com:lisboadouglas/edgefinc.git
cd edge-backend
```

2. Instalar dependências
```bash
composer install
```

3. Configurar variáveis de ambiente
Crie uma cópia do `.env.example` para `.env`:
```bash
cp .env.example .env
```

4. Edite o `.env` com as configurações de banco de dados do seu ambiente:
```bash
APP_URL=http://localhost:8000 #Sua url de ambiente
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edge_finc #seu banco de dados
DB_USERNAME=user #seu usuário
DB_PASSWORD=pass #sua senha
```
5. Gere a chave e as hash JWT
```bash
php artisan key:generate
php artisan jwt:secret #para gerar a hash do JWT
php artisan jwt:token-api #para gerar a hash que deve ser utilizada para consumir a rota de API (Authorization Bearer)
```

> A hash `API_STATIC_KEY` deverá ser adicionada ao `.env` do projeto, ou ser utilizada no POSTMAN:

6. Migrar banco de dados

```bash
php artisan migrate
```

7. Otimizar a aplicação
```bash
php artisan optimize
```



### 🖥 Executar aplicação
```bash
php artisan serve --port=8000
```

Envie o CPF para a rota: http://localhost:8000/api/offers

### 🔒 Segurança
- A rota requer token JWT no header
```http
Authorization: Bearer {API_STATIC_TOKEN}
```

### Rotação de Token
Sempre que precisar gerar um novo token estático

```bash
php artisan jwt:token-api #para gerar a hash que deve ser utilizada para consumir a rota de API (Authorization Bearer)
```

Este comando irá gerar um novo token e irá atualizar automaticamente o `.env` e exibirá o token no terminal para poder ser copiado e ser adicionado ao frontend

*Desenvolvido por Douglas S Lisboa
*Licença: MIT
*Repositório do Frontend: [Repositório Frontend](https://github.com/lisboadouglas/frontend-edgefinch)