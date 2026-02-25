Integrações Assíncronas, Mensageria, Queues
Conceito
Tirar chamadas lentas da requisição HTTP, executando em Job de fila (Redis) com retentativas e monitoramento.
​

Na aplicação de treino

IssuePolicyService emite a Policy com integration_status = pending_integration.

Dispara SendPolicyToInsurerJob na fila.

Job chama InsurerClient, atualiza integration_status para confirmed ou error.

Como dizer

“Na primeira mudança de estado crítica — quando a Policy passa para pending_integration — eu disparo um Job numa fila Redis para integrar com a seguradora. Isso deixa a emissão rápida para o usuário e garante retentativas e logs separados. É a mesma disciplina que usei em fluxo de pedidos e estoque no Magento, mas aplicada ao domínio de apólices.”

---
