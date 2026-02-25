Factory
Conceito
Um componente que decide qual classe concreta instanciar/usar, escondendo a lógica de decisão.

Na aplicação de treino
PricingStrategyFactory::forContext($context) retorna a PricingStrategy correta.

Como dizer

“Centralizo a escolha da estratégia em uma Factory. Ela lê o contexto da cotação (origem, destino, idade, plano) e devolve a implementação de PricingStrategy adequada. Assim, se amanhã entrar um produto novo de seguro viagem, eu só crio uma nova Strategy e ajusto a Factory, sem tocar nos controllers.”
​

---
