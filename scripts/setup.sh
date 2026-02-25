#!/bin/bash

echo "🚀 Iniciando setup do Hero Seguros API..."

# Aguardar MySQL estar pronto
echo "⏳ Aguardando MySQL..."
until docker-compose exec -T mysql mysqladmin ping -h localhost -u root -proot &> /dev/null
do
  printf '.'
  sleep 1
done
echo "✅ MySQL pronto!"

# Aguardar Redis estar pronto
echo "⏳ Aguardando Redis..."
until docker-compose exec -T redis redis-cli ping &> /dev/null
do
  printf '.'
  sleep 1
done
echo "✅ Redis pronto!"

# Copiar .env
echo "📝 Configurando .env..."
docker-compose exec -T app cp .env.example .env

# Instalar dependências
echo "📦 Instalando dependências..."
docker-compose exec -T app composer install --no-interaction --no-progress

# Gerar chave
echo "🔑 Gerando chave de aplicação..."
docker-compose exec -T app php artisan key:generate

# Rodar migrações
echo "🗄️  Rodando migrações..."
docker-compose exec -T app php artisan migrate --force

# Seed dados
echo "🌱 Inserindo dados iniciais..."
docker-compose exec -T app php artisan db:seed

# Limpar caches
echo "🧹 Limpando caches..."
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear

echo ""
echo "✨ Setup concluído com sucesso!"
echo ""
echo "📍 URLs importantes:"
echo "   API:        http://localhost:8000/api/v1"
echo "   PHPMyAdmin: http://localhost:8080"
echo ""
echo "💡 Próximos passos:"
echo "   1. Rodar testes: docker-compose exec app composer test"
echo "   2. Acessar shell:  docker-compose exec app bash"
echo "   3. Artisan tinker: docker-compose exec app php artisan tinker"
echo ""
