Тестовое задание для Tifia
==========================

В данном репозитории находится выполненное [тестовое задание](https://bitbucket.org/alexgutnik/test-task/src/master/)
для Tifia.

Порядок установки
1. `git clone git@github.com:dwdraugr/tifia-task.git`
1. `composer install`   
1. `php yii db/make-index` - данный скрипт создаст индексы для таблиц, значительно ускорив выполнение запросов (пункт 1)
1. `php yii help referral` - список всех скриптов с кратким описанием каждого
1. ???
1. PROFIT

## Детали реализации

Основная логика приложения сконцентрированна в `service/referral/ComputingClient.php`, откуда она подключается консольные контроллеры через инъекцию зависимости.
Для простых запросов используются модели, более же сложные запросы написаны вручную.
После разбора `EXPLAIN` нескольких запросов подготовлены индексы для быстрой обработки запросов.
