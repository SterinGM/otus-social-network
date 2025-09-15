## Документация

### Настройка параметров окружения

* Создайте файл _.env.local_ в корне проекта
* Пропишите в этом файле параметры из _.env_
  * **MYSQL_ROOT_PASSWORD**
  * **DATABASE_URL_***
  * **AMQP_PASSWORD**
  * **MESSENGER_TRANSPORT_DSN**
* Поменяйте пароли для своей рабочей копии

### Настройка окружения с помощью Docker

###### Если вы пользуетесь Windows, то вместо `docker-compose exec php` надо использовать `docker-compose exec php php`

Для развертывания окружения нужно:

* Установить Docker - https://docs.docker.com/engine/installation/.
* Выполнить `docker-compose up -d` (для просмотра логов можно использовать команду `docker-compose logs --tail=20 -f`)
* Выполнить команды (для упрощения процесса разработки эти команды можно добавить в External Tools в PhpStorm, и навесить сочетания клавиш)

```bash
docker-compose exec php composer install
```
Если вам необходимо создать БД, то выполните команду

```bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists -c main
```
```bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists -c dialog
```
```bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists -c dialog_0
```
```bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists -c dialog_1
```
Для применения миграций выполните команду

```bash
docker-compose exec php bin/console doctrine:migration:migrate -n --configuration=config/migrations/main.yaml --em=main
```
```bash
docker-compose exec php bin/console doctrine:migration:migrate -n --configuration=config/migrations/dialog.yaml --em=dialog
```
```bash
docker-compose exec php bin/console doctrine:migration:migrate -n --configuration=config/migrations/dialog.yaml --em=dialog_0
```
```bash
docker-compose exec php bin/console doctrine:migration:migrate -n --configuration=config/migrations/dialog.yaml --em=dialog_1
```
Если вам нужны тестовые данные для разработки, то можно загрузить фикстуры  
Процесс генерации занимает значительное время

```bash
docker-compose exec php bin/console doctrine:fixtures:load -n
```
После этого необходимо очистить кеш приложения

```bash
docker-compose exec php bin/console cache:pool:clear cache.app
```

После этого локально проект будет доступен по адресу http://127.0.0.1:8089/

### Шардирование БД диалогов

Для установки границы записи новых чатов в новые шарды выполните команду

```bash
docker-compose exec php bin/console app:dialog:reshard:set-boundary
```

Данные мигрируют строго по одному шарду  
Для запуска миграции в новые шарды выполните команду указав номер шарда и количество новых шардов 0, 1, 2 и тд.

```bash
docker-compose exec php bin/console app:dialog:reshard:migrate-shard 0 2
```

### Запуск WebSocket сервера

Для ручного запуска сервера выполните команду

```bash
docker-compose exec php bin/console app:websocket:serve
```

Сервер будет доступен по адресу `ws://127.0.0.1:8090`

Тестовый клиент находится по адресу http://127.0.0.1:8089/websocket-client.html