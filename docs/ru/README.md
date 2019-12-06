### Тестирование

* По адресу `https://domain.amocrm.ru/settings/widgets/` создать интеграцию
* Выполнить миграции:
  * `php tests/yii api/migrate-app` - приложение
  * `php tests/yii api/migrate-rbac` - rbac
* (Опционально) Создать файл `~/config/params-local.php` и сконфигурировать в нем параметры:
```php
<?php
return [
    'rvkulikov.amo.tests.account.integration_id' => '00000000-0000-0000-0000-000000000000',
    'rvkulikov.amo.tests.account.secret_key'     => 'secret_key',
    'rvkulikov.amo.tests.account.redirect_uri'   => 'https://example.com/api/oauth2/redirect',
];
```  
* Выполнить команду `php tests/yii api/init`, в результате в консоль выведется URL, для перехода вида:
```
https://www.amocrm.ru/oauth/?client_id=00000000-0000-0000-0000-000000000000&state=state
```
* Перейти по этому адресу и выбрать аккаунт для подключения.
* В результате вы будете переброшены на страницу 
`https://example.com/api/oauth2/redirect?code=code&state=state&referer=domain.amocrm.ru`
* Замените `https://example.com` на условный `http://tests.amo-module.loc`, чтобы направить запрос в `~/tests/web/index-test.php`
* Все, аккаунт интегрирован и можно выполнять команды синхронизации.