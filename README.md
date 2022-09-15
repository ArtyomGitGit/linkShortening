# Сервис для сокращения ссылок
В стиле REST API и GraphQL.
Для REST имеется документация на Swagger (/api/documentation).
Базируется на Laravel 9. 
Тесты написаны на Pest

## Развёртывание
- Указать в .env нужные переменные
- docker-compose build
- docker-compose up -d
- выполнить команду composer update в /src
- docker-compose exec php /bin/sh
    - cp .env.example .env
    - php artisan key:generate
    - php artisan migrate

---
## Документация REST API
Домен указан в src/.env в переменной APP_URL

**POST /api/shorten/link** - запрос для создания короткой ссылки. Запрос принимает стороку (ссылку) и возвращает модель сокращенной ссылки.
```
Content-Type: application/json
```

JSON нагрузка:

```
{
    "link": "ссылка для укорачивания" (string),
}
```

Содержимое ответа:
Content-Type: application/json

```
    "data": {
        "original": "первоначальная ссылка" (string),
        "shortened": "короткий вид первоначальной ссылки" (string),
        "click_count": счетчик переходов по короткой ссылке (int)
    }
```

---
**GET /api/shorten/links** - получить список ранее сокращенных ссылок. Информация о короткой ссылке содержит: полную ссылку, короткую ссылку и счетчик переходов по короткой ссылке.

Содержимое ответа:
```
Content-Type: application/json
[
    {
        "original": "первоначальная ссылка" (string),
        "shortened": "короткий вид первоначальной ссылки" (string),
        "click_count": счетчик переходов по короткой ссылке (int)
    }
]
```

---
## Документация GraphQL

Точка входа - /graphql

---
Queries

```
{
    links {
        id,
        original,
        shortened,
        click_count,
        created_at,
        updated_at
    }
}
```

```
{
    link(id: <id>) {
        id,
        original,
        shortened,
        click_count,
        created_at,
        updated_at
    }
}
```

---
Mutation

```
mutation {
  shortenLink(
    original: <"ссылка для сокращения">
  ) {
        id,
        original,
        shortened,
        click_count,
        created_at,
        updated_at
  }
}
```