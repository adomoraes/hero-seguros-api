# Estrutura de Diretórios

Crie a seguinte estrutura de diretórios no seu projeto:

```
hero-seguros-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── V1/
│   │   │       └── V2/
│   │   ├── Requests/
│   │   ├── Resources/
│   │   └── Middleware/
│   ├── Models/
│   ├── Repositories/
│   │   └── Contracts/
│   ├── Services/
│   ├── Strategies/
│   ├── Jobs/
│   ├── Events/
│   ├── Listeners/
│   ├── Exceptions/
│   ├── Enums/
│   ├── Traits/
│   ├── Providers/
│   └── Constants/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php
│   └── web.php
├── resources/
│   └── views/
├── storage/
│   ├── logs/
│   ├── app/
│   │   └── public/
│   └── framework/
│       ├── sessions/
│       ├── views/
│       └── cache/
├── bootstrap/
│   └── cache/
├── tests/
│   ├── Unit/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Strategies/
│   │   └── Events/
│   ├── Feature/
│   │   ├── Quotations/
│   │   ├── Destinations/
│   │   ├── Authentication/
│   │   └── Health/
│   ├── Pest.php
│   └── TestCase.php
├── docker/
│   ├── php/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   └── www.conf
│   └── mysql/
│       └── my.cnf
├── scripts/
│   └── setup.sh
├── public/
│   └── index.php
├── .env.example
├── .gitignore
├── docker-compose.yml
├── Dockerfile
├── composer.json
├── composer.lock
├── phpunit.xml
├── pint.json
├── .editorconfig
├── .styleci.yml
└── README.md
```

## Criar Diretórios Base

```bash
# Entrar no container
docker-compose exec app bash

# Criar estrutura
mkdir -p app/{Http/Controllers/Api/{V1,V2},Http/{Requests,Resources,Middleware},Repositories/Contracts,Services,Strategies,Jobs,Events,Listeners,Exceptions,Enums,Traits,Providers,Constants}
mkdir -p database/{migrations,seeders,factories}
mkdir -p storage/{logs,app/public,framework/{sessions,views,cache}}
mkdir -p bootstrap/cache
mkdir -p tests/{Unit/{Services,Repositories,Strategies,Events},Feature/{Quotations,Destinations,Authentication,Health}}
mkdir -p docker/{php,mysql}
mkdir -p scripts
mkdir -p resources/views
mkdir -p public

# Dar permissões
chmod -R 775 storage bootstrap/cache

exit
```

## Permissões Docker

Se estiver rodando em Linux, as permissões dos arquivos podem precisar ser ajustadas:

```bash
sudo chown -R $USER:$USER .
chmod -R 755 docker scripts
chmod +x scripts/setup.sh
```
