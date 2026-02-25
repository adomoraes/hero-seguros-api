# 🏛️ Padrões de Projeto e Arquitetura

Este documento descreve os padrões de projeto utilizados na Hero Seguros API.

## 📦 Repository Pattern

O Repository Pattern abstrai a camada de dados, permitindo fácil substituição de implementações.

### 1. Interface (Contract)

```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Quotation;

interface QuotationRepositoryInterface
{
    public function findById(int $id): ?Quotation;
    
    public function findByUserId(int $userId): array;
    
    public function store(array $data): Quotation;
    
    public function update(Quotation $quotation, array $data): Quotation;
    
    public function delete(Quotation $quotation): bool;
    
    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Pagination\Paginator;
}
```

### 2. Implementação

```php
<?php

namespace App\Repositories;

use App\Models\Quotation;
use App\Repositories\Contracts\QuotationRepositoryInterface;

class QuotationRepository implements QuotationRepositoryInterface
{
    public function __construct(private Quotation $model) {}

    public function findById(int $id): ?Quotation
    {
        return $this->model->find($id);
    }

    public function findByUserId(int $userId): array
    {
        return $this->model->where('user_id', $userId)->get()->toArray();
    }

    public function store(array $data): Quotation
    {
        return $this->model->create($data);
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        $quotation->update($data);
        return $quotation->fresh();
    }

    public function delete(Quotation $quotation): bool
    {
        return $quotation->delete();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        $query = $this->model->query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->paginate($perPage);
    }
}
```

### 3. Registro no Container

No `app/Providers/AppServiceProvider.php`:

```php
public function register(): void
{
    $this->app->bind(
        QuotationRepositoryInterface::class,
        QuotationRepository::class
    );
}
```

### 4. Uso em Controllers

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\Contracts\QuotationRepositoryInterface;

class QuotationController
{
    public function __construct(
        private QuotationRepositoryInterface $repository
    ) {}

    public function index()
    {
        return $this->repository->paginate();
    }

    public function show(int $id)
    {
        return $this->repository->findById($id);
    }
}
```

---

## 🎯 Strategy Pattern

O Strategy Pattern permite encapsular diferentes algoritmos de cálculo de preço.

### 1. Interface de Estratégia

```php
<?php

namespace App\Strategies;

use App\Models\Quotation;

interface PricingStrategyInterface
{
    public function calculate(Quotation $quotation): float;
}
```

### 2. Implementações Concretas

```php
<?php

namespace App\Strategies;

use App\Models\Quotation;

class StandardPricingStrategy implements PricingStrategyInterface
{
    public function calculate(Quotation $quotation): float
    {
        $basePrice = 50.00;
        $daysMultiplier = $quotation->days * 2.50;
        $riskFactor = $quotation->destination->risk_factor ?? 1.0;
        
        return ($basePrice + $daysMultiplier) * $riskFactor;
    }
}

class PremiumPricingStrategy implements PricingStrategyInterface
{
    public function calculate(Quotation $quotation): float
    {
        // Lógica diferente para preço premium
        $basePrice = 100.00;
        $daysMultiplier = $quotation->days * 5.00;
        $riskFactor = ($quotation->destination->risk_factor ?? 1.0) * 1.5;
        $coverageBonus = 25.00; // Cobertura adicional
        
        return ($basePrice + $daysMultiplier + $coverageBonus) * $riskFactor;
    }
}

class EconomyPricingStrategy implements PricingStrategyInterface
{
    public function calculate(Quotation $quotation): float
    {
        // Lógica para preço economia
        $basePrice = 25.00;
        $daysMultiplier = $quotation->days * 1.50;
        $riskFactor = $quotation->destination->risk_factor ?? 1.0;
        
        return ($basePrice + $daysMultiplier) * $riskFactor;
    }
}
```

### 3. Serviço que usa Estratégia

```php
<?php

namespace App\Services;

use App\Models\Quotation;
use App\Strategies\PricingStrategyInterface;

class QuotationPricingService
{
    public function __construct(
        private PricingStrategyInterface $strategy
    ) {}

    public function calculate(Quotation $quotation): float
    {
        return $this->strategy->calculate($quotation);
    }

    public function setStrategy(PricingStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }
}
```

### 4. Factory para criar estratégia apropriada

```php
<?php

namespace App\Strategies;

class PricingStrategyFactory
{
    public static function make(string $planType): PricingStrategyInterface
    {
        return match ($planType) {
            'standard' => app(StandardPricingStrategy::class),
            'premium' => app(PremiumPricingStrategy::class),
            'economy' => app(EconomyPricingStrategy::class),
            default => app(StandardPricingStrategy::class),
        };
    }
}
```

### 5. Uso em Controllers

```php
public function store(StoreQuotationRequest $request, QuotationPricingService $pricingService)
{
    $quotation = $this->repository->store($request->validated());
    
    // Usar estratégia apropriada
    $strategy = PricingStrategyFactory::make($request->plan_type);
    $pricingService->setStrategy($strategy);
    
    $premium = $pricingService->calculate($quotation);
    
    $quotation->update(['premium' => $premium]);
    
    return response()->json($quotation, 201);
}
```

---

## 💉 Dependency Injection

Injetar dependências permite melhor testabilidade e desacoplamento.

### 1. Injeção em Constructores

```php
<?php

namespace App\Services;

use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Strategies\PricingStrategyFactory;
use Illuminate\Log\Logger;

class QuotationService
{
    // Dependências injetadas automaticamente
    public function __construct(
        private QuotationRepositoryInterface $repository,
        private QuotationPricingService $pricingService,
        private Logger $logger,
    ) {}

    public function createAndPrice(array $data): \App\Models\Quotation
    {
        try {
            $quotation = $this->repository->store($data);
            
            $strategy = PricingStrategyFactory::make($data['plan_type']);
            $this->pricingService->setStrategy($strategy);
            
            $premium = $this->pricingService->calculate($quotation);
            $quotation->update(['premium' => $premium]);
            
            $this->logger->info("Quotation created", [
                'quotation_id' => $quotation->id,
                'premium' => $premium
            ]);
            
            return $quotation;
        } catch (\Exception $e) {
            $this->logger->error("Failed to create quotation", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
```

### 2. Resolução Automática

Laravel resolve automaticamente dependências tipadas:

```php
// No AppServiceProvider.php (registrar bindings)
public function register(): void
{
    $this->app->bind(QuotationRepositoryInterface::class, QuotationRepository::class);
    $this->app->singleton(QuotationPricingService::class);
}

// No Controller (Laravel injeta automaticamente)
public function __construct(QuotationService $service)
{
    $this->service = $service;
    // Laravel automaticamente cria QuotationService com todas suas dependências
}
```

### 3. Service Provider Customizado

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Repositories\QuotationRepository;
use App\Services\QuotationService;
use App\Strategies\PricingStrategyFactory;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            QuotationRepositoryInterface::class,
            QuotationRepository::class
        );

        // Services
        $this->app->bind(QuotationService::class, function ($app) {
            return new QuotationService(
                $app->make(QuotationRepositoryInterface::class),
                $app->make(QuotationPricingService::class),
                $app->make('log')
            );
        });

        // Singleton (uma única instância)
        $this->app->singleton(QuotationPricingService::class);
    }

    public function boot(): void
    {
        // Executado após registrar tudo
    }
}
```

---

## 📨 Service Layer

A Service Layer contém a lógica de negócio complexa, isolada dos Controllers.

### Estrutura Recomendada

```
app/Services/
├── QuotationService.php           # Lógica principal
├── QuotationPricingService.php    # Cálculo de preços
├── DestinationService.php         # Gerenciamento de destinos
└── NotificationService.php        # Envio de notificações
```

### Exemplo de Service

```php
<?php

namespace App\Services;

use App\Models\Quotation;
use App\Repositories\Contracts\QuotationRepositoryInterface;
use App\Events\QuotationCreated;
use App\Jobs\ProcessQuotation;
use Illuminate\Support\Facades\Log;

class QuotationService
{
    public function __construct(
        private QuotationRepositoryInterface $repository,
        private QuotationPricingService $pricingService,
    ) {}

    /**
     * Criar cotação com validações de negócio
     */
    public function create(array $data): Quotation
    {
        // Validação de negócio
        $this->validateDestination($data['destination_id']);
        $this->validateDates($data['start_date'], $data['end_date']);

        // Criar cotação
        $quotation = $this->repository->store($data);

        // Calcular preço
        $premium = $this->pricingService->calculate($quotation);
        $quotation->update(['premium' => $premium]);

        // Disparar evento
        event(new QuotationCreated($quotation));

        // Fila assíncrona
        ProcessQuotation::dispatch($quotation);

        Log::info("Quotation created", [
            'id' => $quotation->id,
            'user_id' => $quotation->user_id,
        ]);

        return $quotation;
    }

    /**
     * Atualizar status de cotação
     */
    public function approve(Quotation $quotation): Quotation
    {
        $quotation->update(['status' => 'approved']);
        event(new QuotationApproved($quotation));
        return $quotation;
    }

    /**
     * Validações de negócio
     */
    private function validateDestination(int $destinationId): void
    {
        $destination = Destination::find($destinationId);
        
        if (!$destination) {
            throw new \App\Exceptions\DestinationNotFoundException();
        }
    }

    private function validateDates(string $startDate, string $endDate): void
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        if ($start->greaterThanOrEqualTo($end)) {
            throw new \App\Exceptions\InvalidDateRangeException();
        }

        if ($start->lessThan(now())) {
            throw new \App\Exceptions\PastDateException();
        }
    }
}
```

---

## 🎪 Jobs e Queues

Jobs são tarefas assíncronas processadas em background.

### 1. Criar um Job

```bash
docker-compose exec app php artisan make:job ProcessQuotation
```

### 2. Implementar Job

```php
<?php

namespace App\Jobs;

use App\Models\Quotation;
use App\Notifications\QuotationProcessedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessQuotation implements ShouldQueue
{
    use Queueable;

    // Configurações de retry
    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 10;

    public function __construct(private Quotation $quotation) {}

    public function handle(): void
    {
        try {
            Log::info("Processing quotation", ['id' => $this->quotation->id]);

            // Simulação de processamento pesado
            sleep(2);

            // Atualizar status
            $this->quotation->update(['status' => 'processed']);

            // Enviar notificação
            $this->quotation->user->notify(
                new QuotationProcessedNotification($this->quotation)
            );

            Log::info("Quotation processed successfully", [
                'id' => $this->quotation->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to process quotation", [
                'id' => $this->quotation->id,
                'error' => $e->getMessage()
            ]);
            throw $e; // Retry on exception
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical("Quotation processing failed after retries", [
            'id' => $this->quotation->id,
            'error' => $exception->getMessage()
        ]);

        $this->quotation->update(['status' => 'failed']);
    }
}
```

### 3. Disparar Job

```php
// Imediato
ProcessQuotation::dispatch($quotation);

// Com delay
ProcessQuotation::dispatch($quotation)->delay(now()->addMinutes(5));

// Com conexão e fila específicas
ProcessQuotation::dispatch($quotation)->onConnection('redis')->onQueue('high');
```

### 4. Monitorar Queue

```bash
# Rodar worker
docker-compose exec app php artisan queue:work redis

# Com limit de jobs
docker-compose exec app php artisan queue:work redis --max-jobs=100

# Com max time
docker-compose exec app php artisan queue:work redis --max-time=3600
```

---

## 📋 Form Requests (Validação)

Form Requests centralizam a validação de entrada de dados.

### Criar Request

```bash
docker-compose exec app php artisan make:request StoreQuotationRequest
```

### Implementação

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'destination_id' => 'required|exists:destinations,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'plan_type' => [
                'required',
                Rule::in(['economy', 'standard', 'premium']),
            ],
            'travelers' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'destination_id.required' => 'Destino é obrigatório',
            'start_date.after' => 'Data inicial deve ser no futuro',
            'end_date.after' => 'Data final deve ser posterior à inicial',
        ];
    }

    public function prepareForValidation(): void
    {
        // Transformar dados antes de validar
        $this->merge([
            'travelers' => (int) $this->travelers,
        ]);
    }
}
```

---

## 🧪 Testando com TDD

Test-Driven Development: escrever testes antes do código.

### Teste Unitário

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QuotationPricingService;
use App\Strategies\StandardPricingStrategy;
use App\Models\Quotation;

class QuotationPricingServiceTest extends TestCase
{
    private QuotationPricingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuotationPricingService(
            new StandardPricingStrategy()
        );
    }

    public function test_can_calculate_premium()
    {
        $quotation = Quotation::factory()->create([
            'days' => 7,
        ]);

        $premium = $this->service->calculate($quotation);

        $this->assertGreaterThan(0, $premium);
        $this->assertIsFloat($premium);
    }

    public function test_premium_increases_with_risk_factor()
    {
        $quotation1 = Quotation::factory()
            ->for(Destination::factory()->create(['risk_factor' => 1.0]))
            ->create(['days' => 7]);

        $quotation2 = Quotation::factory()
            ->for(Destination::factory()->create(['risk_factor' => 1.5]))
            ->create(['days' => 7]);

        $premium1 = $this->service->calculate($quotation1);
        $premium2 = $this->service->calculate($quotation2);

        $this->assertLessThan($premium2, $premium1);
    }
}
```

### Teste de Feature/Integração

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Destination;

class QuotationTest extends TestCase
{
    public function test_can_create_quotation()
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/quotations', [
            'destination_id' => $destination->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(8),
            'plan_type' => 'standard',
            'travelers' => 2,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'premium', 'status']
        ]);
    }

    public function test_cannot_create_quotation_with_invalid_dates()
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/quotations', [
            'destination_id' => $destination->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(8),
            'plan_type' => 'standard',
            'travelers' => 2,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start_date']);
    }
}
```

---

## 📚 Resumo dos Padrões

| Padrão | Propósito | Exemplo |
|--------|----------|---------|
| **Repository** | Abstração de dados | QuotationRepository |
| **Strategy** | Múltiplos algoritmos | PricingStrategy |
| **Service Layer** | Lógica de negócio | QuotationService |
| **Dependency Injection** | Desacoplamento | Constructor injection |
| **Factory** | Criar objetos | PricingStrategyFactory |
| **Observer** | Eventos | QuotationCreated event |
| **Job/Queue** | Processamento assíncrono | ProcessQuotation job |
| **Form Request** | Validação centralizada | StoreQuotationRequest |
| **TDD** | Testes primeiro | QuotationServiceTest |

---

Estes padrões garantem código **testável, mantível e escalável** ✨
