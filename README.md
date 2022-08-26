# REST API для сокращения ссылок 
Базируется на Laravel 9.

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
## Описание API
Домен указан в src/.env в переменной APP_URL

**POST /api/shorten/link** - запрос для создания короткой ссылки. Запрос принимает стороку (ссылку) и возвращает сокращенную ссылку.
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
"Сформированная короткая ссылка" (string)
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