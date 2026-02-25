TDD e Código Testável
Conceito
Escrever testes pensando na regra de negócio, apoiado por design de código simples e coeso.

Na aplicação de treino

Testes de QuoteService com diferentes contextos de precificação.

Testes de IssuePolicyService validando estados de Quote (calculated, expirado).

Teste de que o Job é disparado ao emitir uma Policy.

Como dizer

“Eu gosto de escrever testes nos serviços de aplicação. No fluxo de cotação e apólice, por exemplo, consigo testar QuoteService e IssuePolicyService isoladamente, mockando os Repositories. Isso garante que a regra de negócio está correta, independente de Eloquent ou HTTP.”

---

