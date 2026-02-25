# 🚀 Guia de Inicialização

Este documento descreve como começar com o Hero Seguros API após o setup inicial.

## ✅ Pré-requisitos

- Docker e Docker Compose instalados
- Git configurado
- Editor de código (VSCode, PhpStorm, etc)

## 📦 Instalação Rápida

### 1. Clone e configure o projeto

```bash
# Clonar repositório
git clone https://github.com/seu-usuario/hero-seguros-api.git
cd hero-seguros-api

# Copiar arquivo de ambiente
cp .env.example .env

# Dar permissão ao script de setup
chmod +x scripts/setup.sh
```

### 2. Rodar setup automático

```bash
docker-compose down -v  # Limpar containers antigos (se houver)
docker-compose up -d
bash scripts/setup.sh
```

**Ou setup manual:**

```bash
# Subir containers
docker-compose up -d

# Entrar no container
docker-compose exec app bash

# Dentro do container
composer install
php artisan key:generate
php artisan migrate --seed
exit
```

### 3. Verificar que tudo está funcionando

```bash
# Testar conexão com API
curl http://localhost:8000/api/v1/health

# Resposta esperada:
# {"status":"ok","timestamp":"2026-02-23T14:00:00Z"}

# Acessar PHPMyAdmin
# http://localhost:8080
# Usuário: root
# Senha: root
```

## 🏗️ Estrutura de Diretórios

Após o setup, os principais diretórios estão organizados assim:

```
app/
├── Http/
│   ├── Controllers/          # Controllers da API
│   ├── Requests/             # Form Requests (validação)
│   └── Middleware/           # Middlewares customizados
├── Models/                    # Modelos Eloquent
├── Repositories/             # Repository Pattern
├── Services/                 # Lógica de negócio
├── Strategies/              # Strategy Pattern
├── Jobs/                    # Jobs assíncronos
└── Events/                  # Domain Events

database/
├── migrations/              # Estrutura do banco
├── seeders/                 # Dados iniciais
└── factories/               # Factories para testes

tests/
├── Unit/                    # Testes unitários
├── Feature/                 # Testes de integração
└── Pest.php                # Setup do Pest
```

## 💻 Primeiros Comandos

### Artisan Tinker (REPL PHP)

```bash
docker-compose exec app php artisan tinker

# Dentro do tinker
>>> User::all();
>>> User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('secret')]);
>>> exit()
```

### Criar um novo Model com scaffolding completo

```bash
docker-compose exec app php artisan make:model Quotation -mfcs
# Cria: Model, Migration, Factory, Controller, Seeder
```

### Criar um Job assíncrono

```bash
docker-compose exec app php artisan make:job ProcessQuotation
```

### Rodar migrações

```bash
# Rodar todas
docker-compose exec app php artisan migrate

# Rodar com seed
docker-compose exec app php artisan migrate --seed

# Rollback última batch
docker-compose exec app php artisan migrate:rollback

# Fresh (limpa tudo e executa)
docker-compose exec app php artisan migrate:fresh --seed
```

## 🧪 Rodar Testes

### PHPUnit

```bash
# Todos os testes
docker-compose exec app php artisan test

# Com coverage
docker-compose exec app php artisan test --coverage

# Teste específico
docker-compose exec app php artisan test tests/Unit/Services/QuotationServiceTest.php

# Com filtro
docker-compose exec app php artisan test --filter=QuotationServiceTest
```

### Pest (sintaxe mais moderna)

```bash
# Todos os testes
docker-compose exec app composer test:pest

# Teste específico
docker-compose exec app ./vendor/bin/pest tests/Unit/Services/

# Com coverage
docker-compose exec app ./vendor/bin/pest --coverage
```

## 📝 Exemplos de Desenvolvimento

### 1. Criar um novo Controller

```bash
docker-compose exec app php artisan make:controller Api/V1/QuotationController --api
```

### 2. Criar validações (Form Request)

```bash
docker-compose exec app php artisan make:request StoreQuotationRequest
```

### 3. Criar um Repository

```bash
# Criar classe
touch app/Repositories/QuotationRepository.php

# E sua interface
touch app/Repositories/Contracts/QuotationRepositoryInterface.php
```

### 4. Criar um Service

```bash
# Criar classe de serviço
touch app/Services/QuotationService.php
```

### 5. Criar uma Strategy

```bash
# Interface
touch app/Strategies/PricingStrategyInterface.php

# Implementações
touch app/Strategies/StandardPricingStrategy.php
touch app/Strategies/PremiumPricingStrategy.php
```

## 🔄 Workflow de Desenvolvimento

### 1. Criar migration e model

```bash
docker-compose exec app php artisan make:model Quotation -m
```

### 2. Editar migration (database/migrations/xxxx_create_quotations_table.php)

```php
public function up()
{
    Schema::create('quotations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('destination');
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('premium', 10, 2);
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
    });
}
```

### 3. Rodar migration

```bash
docker-compose exec app php artisan migrate
```

### 4. Criar factory para testes

```bash
docker-compose exec app php artisan make:factory QuotationFactory
```

### 5. Criar seeder

```bash
docker-compose exec app php artisan make:seeder QuotationSeeder
```

### 6. Criar controller

```bash
docker-compose exec app php artisan make:controller Api/V1/QuotationController --api
```

### 7. Criar testes

```bash
docker-compose exec app php artisan make:test Feature/QuotationTest
```

### 8. Implementar lógica e testes

- TDD: Escrever teste primeiro
- Implementar para passar no teste
- Refatorar código

## 📊 Monitorar Queues

```bash
# Rodar queue worker (processa jobs em background)
docker-compose exec app php artisan queue:work redis

# Com múltiplas tentativas
docker-compose exec app php artisan queue:work redis --tries=3

# Listar jobs falhados
docker-compose exec app php artisan queue:failed

# Reprocessar jobs falhados
docker-compose exec app php artisan queue:retry all
```

## 🔍 Debugging

### Laravel Debugbar (para web)

```bash
# Instalar (opcional)
docker-compose exec app composer require barryvdh/laravel-debugbar --dev
```

### Logs

```bash
# Ver logs em tempo real
docker-compose exec app tail -f storage/logs/laravel.log

# Com follow mode
docker-compose exec app tail -200f storage/logs/laravel.log
```

### MySQL

```bash
# Acessar MySQL direto
docker-compose exec mysql mysql -u root -proot hero_seguros

# Ou via PHPMyAdmin
# http://localhost:8080
```

## 📤 Git Workflow (Git Flow)

```bash
# Criar feature branch
git checkout -b feature/nova-funcionalidade

# Fazer commits semânticos
git commit -m "feat: adiciona novo endpoint de cotação"
git commit -m "test: adiciona testes para novo endpoint"
git commit -m "refactor: melhora estrutura de repositories"

# Push para origin
git push origin feature/nova-funcionalidade

# Abrir PR no GitHub
# Revisar, fazer merge

# Voltar para main e atualizar
git checkout main
git pull origin main
```

## ✨ Próximos Passos após Setup

1. **Explorar a documentação**
   - Ler `README.md` completo
   - Entender arquitetura em `docs/`

2. **Criar primeira feature**
   - Implementar um CRUD simples
   - Escrever testes com TDD
   - Usar Repository Pattern

3. **Estudar padrões utilizados**
   - Repository Pattern
   - Strategy Pattern
   - Dependency Injection
   - Service Layer

4. **Implementar queues**
   - Criar um Job assíncrono
   - Disparar a partir de um evento
   - Monitorar execução

5. **Melhorar cobertura de testes**
   - Adicionar testes unitários
   - Adicionar testes de integração
   - Atingir 80%+ de coverage

## 🆘 Troubleshooting

### Erro ao subir containers

```bash
# Limpar volumes e networks
docker-compose down -v

# Subir novamente
docker-compose up -d
```

### Erro de permissão nos arquivos

```bash
# Linux
sudo chown -R $USER:$USER .
chmod -R 755 docker scripts
chmod +x scripts/setup.sh
```

### Erro de conexão com MySQL

```bash
# Verificar logs
docker-compose logs mysql

# Reiniciar MySQL
docker-compose restart mysql
```

### Erro ao instalar dependências

```bash
# Limpar cache do composer
docker-compose exec app composer clear-cache

# Reinstalar
docker-compose exec app composer install --no-cache
```

## 📚 Recursos Úteis

- [Laravel Documentation](https://laravel.com/docs)
- [Pest Documentation](https://pestphp.com)
- [PHPUnit Documentation](https://phpunit.de)
- [Design Patterns in PHP](https://refactoring.guru/design-patterns/php)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)

---

**Dúvidas?** Consulte a documentação ou abra uma issue no repositório!
