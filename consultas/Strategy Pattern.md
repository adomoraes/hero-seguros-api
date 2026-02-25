Strategy Pattern
Conceito
Encapsular várias formas de executar uma regra atrás de uma interface comum. Você escolhe a estratégia em tempo de execução.

Na aplicação de treino

Interface PricingStrategy::calculate(QuoteContext $context).

Implementações: DomesticPricingStrategy, InternationalPricingStrategy, SeniorPricingStrategy.

PricingStrategyFactory decide qual usar com base em destino, idade, plano.

Como dizer

“Uso Strategy para precificação: tenho uma interface PricingStrategy e implementações para diferentes regras de preço, como viagens nacionais, internacionais ou sênior. O QuoteService não conhece os if/else de regra; ele só pede para a fábrica a estratégia certa e chama calculate. Isso facilita evoluir regras sem quebrar o restante do código.”
​

---
