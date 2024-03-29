# amo-module
Модуль для синхронизации всех данных из amocrm в локальную postgresql базу.  

### todo

Пользовательский интерфейс:
* [ ] Авторизация
* [ ] Добавление интеграций
* [ ] Метрики системы

Планировщик:
* [ ] План синхронизации каждой сущности - раз в n секунд
* [ ] Синхронизация удаленных сущностей - по разрывам в рядах id
* [ ] Автозапуск задач на синхронизацию - cron и очереди

Фикстуры:
* [ ] Смена тестового аккаунта раз в 2 недели
* [ ] Фикстуры для наполнения аккаунта тестовыми данными

ci/cd:
* [ ] docker-окружение
* [ ] запуск шины на nodejs
* [ ] запуск локального вебсервера

Резервное копирование:
* [ ] Ежедневная резервная копия схемы средствами плагинов postgresql

Проксирующая шина запросов:
* [ ] Группы запросов с выделенными лимитами на rps
* [ ] Работа с мьютексами
* [ ] Вынесение шины в отдельный пакет

Общее:
* [ ] Координаты контактов, лидов, компаний, покупателей
  * [ ] Сохранение в отдельные поля
  * [ ] Возможность поиска через postgis

Синхронизация данных:
* [x] account
* [x] users
* [x] groups
* [x] custom_fields
* [x] pipelines
* [x] statuses
* [ ] tasks
* [x] note_types
* [ ] notes
* [x] task_types
* [ ] contacts
* [ ] companies
* [ ] leads
* [ ] customers
* [ ] catalogs
* [ ] catalog_elements
* [ ] webhooks

Обработка вебхуков
* [ ] lead
  * [ ] add_lead
  * [ ] update_lead
  * [ ] delete_lead
  * [ ] restore_lead
  * [ ] status_lead
  * [ ] responsible_lead
  * [ ] note_lead
* [ ] contact
  * [ ] add_contact
  * [ ] update_contact
  * [ ] delete_contact
  * [ ] restore_contact
  * [ ] responsible_contact
  * [ ] note_contact
* [ ] company
  * [ ] add_company
  * [ ] update_company
  * [ ] delete_company
  * [ ] restore_company
  * [ ] responsible_company
  * [ ] note_company
* [ ] customer
  * [ ] add_customer
  * [ ] update_customer
  * [ ] delete_customer
  * [ ] responsible_customer
  * [ ] note_customer
* [ ] task
  * [ ] add_task
  * [ ] update_task
  * [ ] delete_task
  * [ ] responsible_task
* [ ] unsorted
  * [ ] add_unsorted
  * [ ] update_unsorted
  * [ ] delete_unsorted
* [ ] messages?
  * [ ] add_message