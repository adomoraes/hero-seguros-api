Repository Pattern
Conceito
Abstrair o acesso a dados: os serviços falam com um QuoteRepository, não direto com Eloquent/SQL.

Na aplicação de treino

Interfaces: QuoteRepository, PolicyRepository.

Implementações: EloquentQuoteRepository, EloquentPolicyRepository.

Como dizer

“Eu isolo o acesso a dados em Repositories. O QuoteService e o IssuePolicyService trabalham com QuoteRepository e PolicyRepository, sem saber se por baixo é Eloquent ou outra tecnologia. Isso facilita testes com mocks e ajuda na modernização do backend sem acoplar regra de negócio ao ORM.”
​

---
