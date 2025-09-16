## Решардинг БД диалогов

### Описание

Шардинг происходит по `chat_id`   
Благодаря этому происходит оптимальное распределение диалогов по шардам   
Решардинг работает как __zero-downtime__   
Следовательно работу приложения можно не останавливать

### Создание новых шардов

Необходимо отредактировать след конфиги примерно так, как показано ниже  
Старые параметры пока что удалять не надо

Добавьте новые шарды
```
# .env 

MYSQL_DATABASE_DIALOG_0=socnet_dialog_0
MYSQL_DATABASE_DIALOG_1=socnet_dialog_1
```

Добавьте новые урлы для подключения к шардам
```
# .env 

DATABASE_URL_DIALOG_0="mysql://${MYSQL_USER}:${MYSQL_ROOT_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE_DIALOG_0}?serverVersion=${SERVER_VERSION}"
DATABASE_URL_DIALOG_1="mysql://${MYSQL_USER}:${MYSQL_ROOT_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE_DIALOG_1}?serverVersion=${SERVER_VERSION}"
```

Скопируйте урлы подключения, чтобы указать реальные параметры подключения
```
# .env.local

DATABASE_URL_DIALOG_0="mysql://${MYSQL_USER}:${MYSQL_ROOT_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE_DIALOG_0}?serverVersion=${SERVER_VERSION}"
DATABASE_URL_DIALOG_1="mysql://${MYSQL_USER}:${MYSQL_ROOT_PASSWORD}@${MYSQL_HOST}/${MYSQL_DATABASE_DIALOG_1}?serverVersion=${SERVER_VERSION}"
```

Добавьте новые подключения шардам
```yaml
# config/packages/doctrine.yaml

doctrine:
  dbal:
    connections:
      # ...
      dialog_0:
        url: '%env(resolve:DATABASE_URL_DIALOG_0)%'
        driver: 'pdo_mysql'
        profiling_collect_backtrace: '%kernel.debug%'
        use_savepoints: true
      dialog_1:
        url: '%env(resolve:DATABASE_URL_DIALOG_1)%'
        driver: 'pdo_mysql'
        profiling_collect_backtrace: '%kernel.debug%'
        use_savepoints: true
```

Добавьте новые менеджеры сущностей
```yaml
# config/packages/doctrine.yaml

doctrine:
  dbal:
    orm:
      entity_managers:
        # ...
        dialog_0:
          report_fields_where_declared: true
          validate_xml_mapping: true
          naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
          connection: dialog_0
          mappings:
            AppDialog:
              type: attribute
              is_bundle: false
              dir: '%kernel.project_dir%/src/Entity/Dialog'
              prefix: 'App\Entity\Dialog'
              alias: Dialog
        dialog_1:
          report_fields_where_declared: true
          validate_xml_mapping: true
          naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
          connection: dialog_1
          mappings:
            AppDialog:
              type: attribute
              is_bundle: false
              dir: '%kernel.project_dir%/src/Entity/Dialog'
              prefix: 'App\Entity\Dialog'
              alias: Dialog
```

Для создания шардов БД выполните команды указав соответствующие параметры
```bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists -c dialog_0
```
```bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists -c dialog_1
```

Для применения миграций выполните команды

```bash
docker-compose exec php bin/console doctrine:migration:migrate -n --configuration=config/migrations/dialog.yaml --em=dialog_0
```
```bash
docker-compose exec php bin/console doctrine:migration:migrate -n --configuration=config/migrations/dialog.yaml --em=dialog_1
```

После этого у вас появятся новые шарды и можно будет с ними дальше работать

### Подготовка кода приложения

Установите соответствующие значения количества шардов
```php
// src/Service/Dialog/ShardManager.php

class ShardManager
{
    public const int DIALOG_OLD_SHARDS_COUNT = 1;
    public const int DIALOG_NEW_SHARDS_COUNT = 2;
```

Добавьте новые шарды. Старые удалять не надо
```php
// src/Service/Dialog/ShardManager.php

class ShardManager
{
    // ...
    private EntityManagerInterface $emShard0;
    private EntityManagerInterface $emShard1;

    public function __construct(
        // ...
        EntityManagerInterface $emShard0,
        EntityManagerInterface $emShard1
    ) {
        // ...
        $this->emShard0 = $emShard0;
        $this->emShard1 = $emShard1;
    }
```

Раскомментируйте логику на время миграции данных
```php
// src/Service/Dialog/ShardManager.php

class ShardManager
{
    public function getEntityManagerForChat(string $chatId): EntityManagerInterface
    {
        $boundary = $this->getShardingBoundary();

        // Старая стратегия
        if (!$boundary || $chatId < $boundary) {
            $shard = $this->getShardByChatId($chatId, self::DIALOG_OLD_SHARDS_COUNT);

            if (!$this->isShardMigrated($shard)) {
                return $this->getOldShardEntityManager($shard);
            }
        }

        // Новая стратегия
        $shard = $this->getShardByChatId($chatId, self::DIALOG_NEW_SHARDS_COUNT);

        return $this->getNewShardEntityManager($shard);
    }
```

Отредактируйте логику выбора новых и старых шардов
```php
// src/Service/Dialog/ShardManager.php

class ShardManager
{
    public function getOldShardEntityManager(int $shard): EntityManagerInterface
    {
        return match ($shard) {
            0 => $this->em,
            default => throw new InvalidArgumentException('Unknown old shard')
        };
    }

    public function getNewShardEntityManager(int $shard): EntityManagerInterface
    {
        return match ($shard) {
            0 => $this->emShard0,
            1 => $this->emShard1,
            default => throw new InvalidArgumentException('Unknown shard')
        };
    }
```

Укажите значения для параметров конструктора класса ShardManager
```yaml
# config/services.yaml

services:
    # ...
    App\Service\Dialog\ShardManager:
      arguments:
        # ...
        $emShard0: '@doctrine.orm.dialog_0_entity_manager'
        $emShard1: '@doctrine.orm.dialog_1_entity_manager'
```

### Выполнения процесса решардинга

Для установки границы записи новых чатов в новые шарды выполните команду

```bash
docker-compose exec php bin/console app:dialog:reshard:set-boundary
```

Данные мигрируют строго по одному шарду  
Для запуска миграции в новые шарды выполните команду указав номер старого шарда 0, 1, 2 и тд.

```bash
docker-compose exec php bin/console app:dialog:reshard:migrate-shard 0
```

### Переключение на новую логику

Закомментируйте логику после миграции данных
```php
// src/Service/Dialog/ShardManager.php

class ShardManager
{
    public function getEntityManagerForChat(string $chatId): EntityManagerInterface
    {
//        $boundary = $this->getShardingBoundary();
//
//        // Старая стратегия
//        if (!$boundary || $chatId < $boundary) {
//            $shard = $this->getShardByChatId($chatId, self::DIALOG_OLD_SHARDS_COUNT);
//
//            if (!$this->isShardMigrated($shard)) {
//                return $this->getOldShardEntityManager($shard);
//            }
//        }

        // Новая стратегия
        $shard = $this->getShardByChatId($chatId, self::DIALOG_NEW_SHARDS_COUNT);

        return $this->getNewShardEntityManager($shard);
    }
```

Для удаления границы решардинга выполните команду
```bash
docker-compose exec php bin/console app:dialog:reshard:finish
```

---
Теперь неиспользуемые шарды можно убрать из конфигов и потом удалить физически  
Но это делать не обязательно
