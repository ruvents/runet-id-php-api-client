# Официальный API-клиент -RUNET--ID-

## Установка

`$ composer require runet-id/api-client:^2.0@alpha`

## Использование

### RunetId\ApiClient\Client

Отправляет запрос к серверу и получает ответ. Используется библиотека [RUVENTS Http Client](https://bitbucket.org/ruvents/http-client)

```php
<?php
$client = new RunetId\ApiClient\ApiClient([
    // API key (обязательный параметр)
    'key' => 'runetidkey',
    // API secret (обязательный параметр)
    'secret' => 'runetidsecret',
    // использовать https? (по умолчанию: false)
    'secure' => true,
    // хост (по умолчанию: 'api.runet-id.com')
    'host' => 'api.runet-id.com'
]);

// отправка GET-запроса
$client->get(
    // относительный путь метода API (обязательный параметр)
    $path = 'event/section/list',
    // параметры строки запроса
    $query = ['name' => 'value'],
    // заголовки
    $headers = ['name' => 'value']
);

// отправка POST-запроса
$client->post(
    // относительный путь метода API (обязательный параметр)
    $path = 'event/section/list',
    // параметры строки запроса
    $query = ['name' => 'value'],
    // данные (строка, или массив данных)
    $data = ['name' => 'value'],
    // заголовки
    $headers = ['name' => 'value'],
    // файлы
    $files = ['name' => new Ruvents\HttpClient\Request\File($path)]
);
```
Методы `Client::get` и `Client::post` возвращают объект класса `Ruvents\HttpClient\Response\Response`. [Подробнее в документации RUVENTS Http Client](https://bitbucket.org/ruvents/http-client).