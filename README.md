## Документация

### Настройка параметров окружения

* Создайте файл _.env.local_ в корне проекта
* Пропишите в этом файле параметры из _.env_
  * **MYSQL_ROOT_PASSWORD**
  * **DATABASE_URL**
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
docker-compose exec php bin/console doctrine:database:create --if-not-exists
```
Для применения миграций выполните команду
Процесс генерации занимает значительное время

```bash
docker-compose exec php bin/console doctrine:migration:migrate -n
```
Если вам нужны тестовые данные для разработки, то можно загрузить фикстуры

```bash
docker-compose exec php bin/console doctrine:fixtures:load -n
```
После этого необходимо очистить кеш приложения

```bash
docker-compose exec php bin/console cache:pool:clear cache.app
```

После этого локально проект будет доступен по адресу http://127.0.0.1:8089/

### Запуск WebSocket сервера

Для ручного запуска сервера выполните команду

```bash
docker-compose exec php bin/console app:websocket:serve
```
Для просмотра статистики сервера выполните команду

```bash
docker-compose exec php bin/console app:websocket:status
```

Сервер будет доступен по адресу __127.0.0.1:3001__