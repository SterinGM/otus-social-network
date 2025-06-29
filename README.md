## Документация

### Настройка параметров окружения

* Создайте файл _.env.local_ в корне проекта
* Пропишите в этих файлах параметры **MYSQL_ROOT_PASSWORD** и **DATABASE_URL** из _.env_
* Поменяйте пароль для своей рабочей копии

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
docker-compose exec php bin/console doctrine:database:create
```
Для применения миграций выполните команду

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
