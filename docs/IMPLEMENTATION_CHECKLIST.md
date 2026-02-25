# 📋 Checklist de Implementação

Use este checklist para acompanhar o desenvolvimento das funcionalidades da Hero Seguros API.

## ✅ Fase 1: Setup Inicial (CONCLUÍDA)

- [x] Docker compose com PHP 8.3, MySQL 8.0, Redis 7.0
- [x] Dockerfile otimizado
- [x] composer.json com dependências
- [x] .env.example com configurações
- [x] Configurações de cache, queue, database, logging, filesystem
- [x] Scripts de setup automático
- [x] .gitignore
- [x] README.md e documentação
- [x] Estrutura de diretórios

## 🔄 Fase 2: Models & Migrations (PRÓXIMO)

### Models principais
- [ ] User (autenticação)
- [ ] Quotation (cotação de seguro)
- [ ] Destination (destino de viagem)
- [ ] Plan (plano de seguro disponível)
- [ ] RiskFactor (fatores de risco por destino)

### Migrations
- [ ] create_users_table
- [ ] create_quotations_table
- [ ] create_destinations_table
- [ ] create_plans_table
- [ ] create_risk_factors_table
- [ ] create_failed_jobs_table
- [ ] create_personal_access_tokens_table

### Relacionamentos
- [ ] User hasMany Quotation
- [ ] Quotation belongsTo User, Destination, Plan
- [ ] Destination hasMany Quotation, RiskFactor
- [ ] Plan hasMany Quotation

### Seeders
- [ ] UserSeeder
- [ ] DestinationSeeder
- [ ] PlanSeeder
- [ ] RiskFactorSeeder

### Factories
- [ ] UserFactory
- [ ] QuotationFactory
- [ ] DestinationFactory
- [ ] PlanFactory

## 🏗️ Fase 3: Repositories (Repository Pattern)

### Contracts (Interfaces)
- [ ] QuotationRepositoryInterface
- [ ] DestinationRepositoryInterface
- [ ] UserRepositoryInterface
- [ ] PlanRepositoryInterface

### Implementações
- [ ] QuotationRepository
- [ ] DestinationRepository
- [ ] UserRepository
- [ ] PlanRepository

### Bindings no ServiceProvider
- [ ] RepositoryServiceProvider criado
- [ ] Todas as interfaces bound
- [ ] Registrado em config/app.php

## 💼 Fase 4: Services (Lógica de Negócio)

### Serviços principais
- [ ] QuotationService
  - [ ] create()
  - [ ] approve()
  - [ ] reject()
  - [ ] cancel()

- [ ] QuotationPricingService
  - [ ] calculate()
  - [ ] setStrategy()

- [ ] DestinationService
  - [ ] getAvailableDestinations()
  - [ ] getRiskFactors()

- [ ] NotificationService
  - [ ] sendQuotationConfirmation()
  - [ ] sendApprovalNotification()

## 🎯 Fase 5: Strategies (Strategy Pattern)

### Pricing Strategies
- [ ] PricingStrategyInterface
- [ ] StandardPricingStrategy
- [ ] PremiumPricingStrategy
- [ ] EconomyPricingStrategy
- [ ] PricingStrategyFactory

### Validação Strategies (opcional)
- [ ] DestinationValidationStrategy
- [ ] DateValidationStrategy

## 🎛️ Fase 6: Controllers & Rotas

### API V1 Controllers
- [ ] QuotationController
  - [ ] index() - List quotations
  - [ ] store() - Create quotation
  - [ ] show() - Get quotation
  - [ ] update() - Update quotation
  - [ ] destroy() - Delete quotation
  - [ ] approve() - Approve quotation (custom action)

- [ ] DestinationController
  - [ ] index() - List destinations
  - [ ] show() - Get destination with risks

- [ ] PlanController
  - [ ] index() - List plans

- [ ] AuthController
  - [ ] login() - Autenticar usuário
  - [ ] logout() - Desautenticar

### Rotas API
- [ ] /api/v1/auth/login
- [ ] /api/v1/auth/logout
- [ ] /api/v1/quotations (GET, POST)
- [ ] /api/v1/quotations/{id} (GET, PUT, DELETE)
- [ ] /api/v1/quotations/{id}/approve (POST)
- [ ] /api/v1/destinations (GET)
- [ ] /api/v1/destinations/{id} (GET)
- [ ] /api/v1/plans (GET)

## 📋 Fase 7: Form Requests (Validação)

### Request Classes
- [ ] StoreQuotationRequest
  - [ ] destination_id required|exists
  - [ ] start_date required|date|after:today
  - [ ] end_date required|date|after:start_date
  - [ ] plan_type required|in:economy,standard,premium
  - [ ] travelers required|integer|min:1|max:10

- [ ] UpdateQuotationRequest
- [ ] LoginRequest
  - [ ] email required|email
  - [ ] password required|min:6

## 📡 Fase 8: Autenticação & Autorização

### Implementação
- [ ] Sanctum API tokens
- [ ] Middleware de autenticação customizado
- [ ] Guards configurados
- [ ] Policies para autorização
  - [ ] QuotationPolicy (user can only access own quotations)

### Autenticação endpoints
- [ ] POST /api/v1/auth/login
- [ ] POST /api/v1/auth/logout
- [ ] GET /api/v1/auth/me (authenticated user)

## 🔔 Fase 9: Events & Listeners

### Events
- [ ] QuotationCreated
- [ ] QuotationApproved
- [ ] QuotationRejected
- [ ] QuotationProcessed

### Listeners
- [ ] SendQuotationCreatedNotification
- [ ] SendApprovalNotification
- [ ] SendRejectionNotification
- [ ] LogQuotationEvent

### Registration
- [ ] EventServiceProvider configurado

## ⏳ Fase 10: Jobs & Queues

### Jobs
- [ ] ProcessQuotation
  - [ ] Processar cotação assincronamente
  - [ ] Retry: 3 tentativas
  - [ ] Timeout: 60 segundos

- [ ] SendNotificationJob
  - [ ] Enviar notificações assincronamente

- [ ] GenerateReportJob (opcional)
  - [ ] Gerar relatórios em background

### Queue Configuration
- [ ] Redis como queue driver
- [ ] Failed jobs migration criada
- [ ] Queue worker configurado
- [ ] Retry logic implementado

## 🧪 Fase 11: Testes Unitários

### Unit Tests - Services
- [ ] QuotationServiceTest
  - [ ] test_can_create_quotation()
  - [ ] test_can_approve_quotation()
  - [ ] test_validates_destination()
  - [ ] test_validates_dates()

- [ ] QuotationPricingServiceTest
  - [ ] test_can_calculate_premium()
  - [ ] test_different_strategies_return_different_prices()
  - [ ] test_premium_increases_with_risk_factor()

- [ ] DestinationServiceTest
  - [ ] test_returns_available_destinations()
  - [ ] test_returns_risk_factors()

### Unit Tests - Repositories
- [ ] QuotationRepositoryTest
  - [ ] test_can_find_by_id()
  - [ ] test_can_store()
  - [ ] test_can_update()
  - [ ] test_can_delete()
  - [ ] test_can_paginate_with_filters()

- [ ] DestinationRepositoryTest

### Unit Tests - Strategies
- [ ] StandardPricingStrategyTest
- [ ] PremiumPricingStrategyTest
- [ ] EconomyPricingStrategyTest

### Coverage Target
- [ ] 80%+ cobertura de código
- [ ] Todos Services cobertos
- [ ] Todos Repositories cobertos
- [ ] Principais Strategies cobertos

## 🎭 Fase 12: Testes de Integração (Feature)

### Feature Tests - Quotations
- [ ] test_authenticated_user_can_list_quotations()
- [ ] test_authenticated_user_can_create_quotation()
- [ ] test_authenticated_user_can_view_own_quotation()
- [ ] test_authenticated_user_can_update_own_quotation()
- [ ] test_authenticated_user_can_delete_own_quotation()
- [ ] test_user_cannot_access_other_users_quotations()
- [ ] test_invalid_quotation_returns_validation_errors()

### Feature Tests - Destinations
- [ ] test_can_list_destinations()
- [ ] test_can_view_destination_with_risks()

### Feature Tests - Auth
- [ ] test_user_can_login_with_valid_credentials()
- [ ] test_user_cannot_login_with_invalid_credentials()
- [ ] test_authenticated_user_can_logout()
- [ ] test_unauthenticated_user_cannot_access_protected_routes()

### Feature Tests - Jobs
- [ ] test_quotation_job_processes_successfully()
- [ ] test_job_retries_on_failure()

## 📊 Fase 13: Documentation

### API Documentation
- [ ] OpenAPI/Swagger spec criado
- [ ] Endpoints documentados
- [ ] Request/Response examples
- [ ] Authentication requirements
- [ ] Error codes

### Code Documentation
- [ ] Docblocks em todos Services
- [ ] Docblocks em todos Repositories
- [ ] README.md completo
- [ ] DESIGN_PATTERNS.md
- [ ] QUICKSTART.md

### Database Documentation
- [ ] ER Diagram criado
- [ ] Migrations documentadas
- [ ] Relationships explicadas

## 🚀 Fase 14: Deployment & DevOps

### Docker
- [x] Dockerfile otimizado
- [x] docker-compose.yml
- [x] Health checks
- [ ] Nginx config (opcional, para produção)

### CI/CD (GitHub Actions)
- [ ] Workflow para testes automáticos
- [ ] Workflow para lint (Pint)
- [ ] Workflow para análise estática (PHPStan)
- [ ] Deployment workflow

### Ambiente de Produção
- [ ] .env.production exemplo
- [ ] Secrets configurados
- [ ] Database backups strategy
- [ ] Log rotation

## 📈 Fase 15: Melhorias e Otimizações

### Performance
- [ ] Query optimization (N+1 problems)
- [ ] Caching implementado
- [ ] Redis para cache de destinos
- [ ] Database indexes criados

### Security
- [ ] CORS configurado
- [ ] Rate limiting implementado
- [ ] SQL Injection prevention
- [ ] XSS prevention
- [ ] CSRF tokens

### Error Handling
- [ ] Custom exception classes
- [ ] Global exception handler
- [ ] Erro responses estruturadas
- [ ] Logging de erros

### Monitoring
- [ ] Logging centralizado
- [ ] Health check endpoint
- [ ] Métricas de performance
- [ ] Alertas configurados (opcional)

## 🎓 Fase 16: Portfólio & GitHub

### GitHub Setup
- [ ] Repository criado e público
- [ ] README.md completo
- [ ] CONTRIBUTING.md (opcional)
- [ ] LICENSE (MIT)
- [ ] Tags/Releases criadas

### Git History
- [ ] Commits semânticos
- [ ] Branch naming conventions
- [ ] Pull requests documentadas
- [ ] Boas práticas de Git Flow

### Portfolio
- [ ] Projeto linkado no LinkedIn
- [ ] Descrição no GitHub
- [ ] Tecnologias listadas
- [ ] Demonstração/Video (opcional)

---

## 📊 Resumo de Progresso

```
Fase 1  (Setup):          ████████████████████ 100% ✅
Fase 2  (Models):         ░░░░░░░░░░░░░░░░░░░░   0%
Fase 3  (Repositories):   ░░░░░░░░░░░░░░░░░░░░   0%
Fase 4  (Services):       ░░░░░░░░░░░░░░░░░░░░   0%
Fase 5  (Strategies):     ░░░░░░░░░░░░░░░░░░░░   0%
Fase 6  (Controllers):    ░░░░░░░░░░░░░░░░░░░░   0%
Fase 7  (Validação):      ░░░░░░░░░░░░░░░░░░░░   0%
Fase 8  (Auth):           ░░░░░░░░░░░░░░░░░░░░   0%
Fase 9  (Events):         ░░░░░░░░░░░░░░░░░░░░   0%
Fase 10 (Jobs):           ░░░░░░░░░░░░░░░░░░░░   0%
Fase 11 (Unit Tests):     ░░░░░░░░░░░░░░░░░░░░   0%
Fase 12 (Feature Tests):  ░░░░░░░░░░░░░░░░░░░░   0%
Fase 13 (Docs):           ░░░░░░░░░░░░░░░░░░░░   0%
Fase 14 (DevOps):         ░░░░░░░░░░░░░░░░░░░░   0%
Fase 15 (Otimizações):    ░░░░░░░░░░░░░░░░░░░░   0%
Fase 16 (Portfólio):      ░░░░░░░░░░░░░░░░░░░░   0%

TOTAL:                    ░░░░░░░░░░░░░░░░░░░░   6% (1/16 fases)
```

## 📝 Notas

- Cada item checked deve ter testes associados
- Use TDD: teste primeiro, depois implementação
- Commit a cada feature implementada
- Push diariamente para manter histórico
- Revise documentação regularmente

---

**Bom desenvolvimento! 🚀**
