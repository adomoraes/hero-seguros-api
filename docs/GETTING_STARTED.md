# 🎯 Próximos Passos - Guia de Desenvolvimento

Parabéns! Você tem a **estrutura base completa** da Hero Seguros API pronta. Agora vamos começar a implementar as funcionalidades.

## 📍 Você está aqui

✅ **Fase 1** concluída: Estrutura Docker, Composer, Configurações
🔴 **Fase 2** próxima: Models, Migrations, Seeders

---

## 🚀 Iniciando do Zero

### 1. **Clone ou inicialize o repositório**

```bash
# Se for começar do seu repositório
cd ~/projetos/hero-seguros-api

# Se for usar estes arquivos que criei
# Copie todos os arquivos para seu diretório
```

### 2. **Primeiro, subir os containers**

```bash
# Entrar no diretório do projeto
cd hero-seguros-api

# Subir containers
docker-compose up -d

# Verificar que está rodando
docker-compose ps
```

**Resultado esperado:**

```
NAME                    STATUS
hero-seguros-app        Up 2 seconds
hero-seguros-mysql      Up 2 seconds (healthy)
hero-seguros-redis      Up 2 seconds (healthy)
hero-seguros-phpmyadmin Up 2 seconds
```

### 3. **Instalar Laravel Framework**

Como você ainda não tem Laravel instalado, vamos instalar:

```bash
# Entrar no container
docker-compose exec app bash

# Criar projeto Laravel (dentro do container)
composer create-project laravel/laravel . --no-interaction

# Saindo do container
exit
```

⚠️ Isso vai levar alguns minutos...

### 4. **Configurar o projeto**

```bash
# Copiar arquivo de ambiente
docker-compose exec app cp .env.example .env

# Gerar chave
docker-compose exec app php artisan key:generate

# Rodar migrações padrão do Laravel
docker-compose exec app php artisan migrate
```

### 5. **Testar que Laravel está funcionando**

```bash
# Acessar
curl http://localhost:8000

# Ou abrir no navegador
# http://localhost:8000
```

---

## 📚 Sequência Recomendada de Desenvolvimento

### **Semana 1: Estrutura de Dados**

#### Dia 1-2: Criar Models e Migrations

```bash
# Entrar no container
docker-compose exec app bash

# Criar Models com migrations
php artisan make:model User -m
php artisan make:model Destination -m
php artisan make:model Plan -m
php artisan make:model Quotation -m
php artisan make:model RiskFactor -m

exit
```

Depois editar cada migration (em `database/migrations/`) para adicionar os campos:

**User Migration:**

```bash
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```

**Destination Migration:**

```bash
public function up()
{
    Schema::create('destinations', function (Blueprint $table) {
        $table->id();
        $table->string('country');
        $table->string('code')->unique(); // BR, US, FR, etc
        $table->decimal('base_risk_factor', 3, 2)->default(1.0);
        $table->text('description')->nullable();
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
}
```

**Plan Migration:**

```bash
public function up()
{
    Schema::create('plans', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Standard, Premium, Economy
        $table->text('description');
        $table->enum('coverage_type', ['basic', 'standard', 'premium']);
        $table->decimal('daily_rate', 8, 2);
        $table->timestamps();
    });
}
```

**Quotation Migration:**

```bash
public function up()
{
    Schema::create('quotations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('destination_id')->constrained()->onDelete('restrict');
        $table->foreignId('plan_id')->constrained()->onDelete('restrict');
        $table->date('start_date');
        $table->date('end_date');
        $table->integer('travelers')->default(1);
        $table->decimal('premium', 10, 2)->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
        $table->timestamps();
    });
}
```

**RiskFactor Migration:**

```bash
public function up()
{
    Schema::create('risk_factors', function (Blueprint $table) {
        $table->id();
        $table->foreignId('destination_id')->constrained()->onDelete('cascade');
        $table->string('category'); // war, natural_disaster, disease, etc
        $table->decimal('multiplier', 3, 2); // 1.5, 2.0, etc
        $table->text('description')->nullable();
        $table->timestamps();
    });
}
```

Depois rodar:

```bash
docker-compose exec app php artisan migrate
```

#### Dia 3: Criar Models com Relacionamentos

Editar cada Model (em `app/Models/`) para adicionar relacionamentos:

**User.php:**

```bash
public function quotations()
{
    return $this->hasMany(Quotation::class);
}
```

**Destination.php:**

```bash
public function quotations()
{
    return $this->hasMany(Quotation::class);
}

public function riskFactors()
{
    return $this->hasMany(RiskFactor::class);
}
```

**Quotation.php:**

```bash
public function user()
{
    return $this->belongsTo(User::class);
}

public function destination()
{
    return $this->belongsTo(Destination::class);
}

public function plan()
{
    return $this->belongsTo(Plan::class);
}
```

#### Dia 4-5: Criar Factories e Seeders

```bash
docker-compose exec app bash

# Criar factories
php artisan make:factory UserFactory
php artisan make:factory DestinationFactory
php artisan make:factory PlanFactory
php artisan make:factory QuotationFactory

# Criar seeders
php artisan make:seeder DestinationSeeder
php artisan make:seeder PlanSeeder
php artisan make:seeder DatabaseSeeder

exit
```

Implementar factories e seeders para ter dados para testes.

Rodar seed:

```bash
docker-compose exec app php artisan migrate --seed
```

### **Semana 2: Repository Pattern**

#### Dia 1-2: Criar Repositories

```bash
docker-compose exec app bash

# Criar diretórios
mkdir -p app/Repositories/Contracts

# Criar interfaces
touch app/Repositories/Contracts/QuotationRepositoryInterface.php
touch app/Repositories/Contracts/DestinationRepositoryInterface.php

# Criar implementações
touch app/Repositories/QuotationRepository.php
touch app/Repositories/DestinationRepository.php

exit
```

Implementar usando exemplos em `docs/DESIGN_PATTERNS.md`.

#### Dia 3-4: Service Layer

```bash
docker-compose exec app bash

# Criar serviços
touch app/Services/QuotationService.php
touch app/Services/QuotationPricingService.php

exit
```

#### Dia 5: Strategies

```bash
docker-compose exec app bash

# Criar strategies
touch app/Strategies/PricingStrategyInterface.php
touch app/Strategies/StandardPricingStrategy.php
touch app/Strategies/PremiumPricingStrategy.php
touch app/Strategies/EconomyPricingStrategy.php

exit
```

### **Semana 3: Controllers & API**

#### Dia 1-2: Criar Controllers

```bash
docker-compose exec app php artisan make:controller Api/V1/QuotationController --api
docker-compose exec app php artisan make:controller Api/V1/DestinationController --api
docker-compose exec app php artisan make:controller Api/V1/AuthController
```

#### Dia 3-4: Form Requests (Validação)

```bash
docker-compose exec app php artisan make:request StoreQuotationRequest
docker-compose exec app php artisan make:request UpdateQuotationRequest
docker-compose exec app php artisan make:request LoginRequest
```

#### Dia 5: Rotas e Testes

Editar `routes/api.php`:

```bash
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('/quotations', QuotationController::class);
    Route::apiResource('/destinations', DestinationController::class);
});
```

### **Semana 4: Testes & Deployment**

#### Dia 1-2: Unit Tests

```bash
docker-compose exec app php artisan make:test Unit/Services/QuotationServiceTest --unit
docker-compose exec app php artisan make:test Unit/Repositories/QuotationRepositoryTest --unit
```

Implementar testes seguindo `docs/DESIGN_PATTERNS.md`.

#### Dia 3-4: Feature Tests

```bash
docker-compose exec app php artisan make:test Feature/QuotationTest
docker-compose exec app php artisan make:test Feature/AuthTest
```

#### Dia 5: Documentation & GitHub

- Completar documentação da API
- Fazer commits e push para GitHub
- Criar README bonito
- Fazer release tag

---

## 🎓 Ordem de Aprendizado Recomendada

1. **Eloquent** - Entender Models e Migrations
2. **Repositories** - Abstração de dados
3. **Services** - Lógica de negócio
4. **Strategies** - Algoritmos variáveis
5. **Controllers** - Endpoints da API
6. **Testes** - TDD e cobertura
7. **Queues** - Processamento assíncrono (avançado)

---

## 💡 Dicas Importantes

### ✅ Faça isso:

- **TDD**: Escreva testes ANTES do código
- **Commits frequentes**: Commit a cada feature
- **Branches**: Use feature branches (`git checkout -b feature/nome`)
- **Code review**: Faça seus próprios PRs e revise
- **Testes passando**: Nunca commitar com testes falhando

### ❌ Evite:

- Mudar código sem testes
- Commits gigantes (um feature = um commit)
- Pular testes "para depois"
- Código sem documentação
- Usar banco produção para testes

---

## 📊 Tempo Estimado

- **Semana 1** (Modelos): 15-20 horas
- **Semana 2** (Repositories/Services): 20-25 horas
- **Semana 3** (Controllers/API): 20-25 horas
- **Semana 4** (Testes/Docs): 20-25 horas

**Total: ~80-95 horas** (2-3 semanas trabalhando full-time)

---

## 🎯 Meta Final

Ao final deste desenvolvimento você terá:

✅ API REST completa e funcional
✅ Todos padrões de projeto implementados
✅ Testes com 80%+ cobertura
✅ Documentação completa
✅ Código production-ready
✅ GitHub com histórico limpo
✅ Pronto para entrevista na Hero Seguros!

---

## 📞 Dúvidas Frequentes

**P: Preciso instalar Laravel manualmente?**
R: Não! O Dockerfile já inclui Composer. Você usa `docker-compose exec app composer create-project laravel/laravel` para instalar.

**P: Qual Laravel versão usar?**
R: A vaga não especifica, mas Laravel 11 é a mais atual. Se quiser Laravel 10, altere em `composer.json`.

**P: Posso usar Pest em vez de PHPUnit?**
R: Sim! Pest é mais moderno. Está disponível em `require-dev` no `composer.json`.

**P: Como rodar os testes?**
R: `docker-compose exec app php artisan test` ou `composer test`

**P: Preciso usar Redis de verdade?**
R: Não para desenvolvimento. Pode usar `sync` em `.env` para testes. Redis é para produção.

**P: Posso colocar em produção?**
R: Com melhorias sim (Nginx, SSL, env vars sensíveis, etc). Mas para portfólio, local/Docker é suficiente.

---

## 📚 Documentos de Referência

- `docs/QUICKSTART.md` - Guia rápido de setup
- `docs/DESIGN_PATTERNS.md` - Padrões implementados
- `docs/DIRECTORY_STRUCTURE.md` - Estrutura de pastas
- `docs/IMPLEMENTATION_CHECKLIST.md` - Checklist de desenvolvimento
- `README.md` - Overview do projeto

---

**Pronto para começar? 🚀 Bora codar!**

Próximo comando:

```bash
docker-compose up -d
docker-compose exec app bash
composer create-project laravel/laravel . --no-interaction
exit
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

Sucesso! 💪
