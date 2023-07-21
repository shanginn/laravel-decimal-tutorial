# Пример использования типа Decimal для хранения денег в Laravel на PHP 8

## Зачем?
Надеюсь, позже напишу об этом подробнее, но если кратко - делать математические операции
на числах с плавающей точкой (`float`, `double`) приводят к неожиданным результатам, а когда речь идет о деньгах,
результату лучше быть предсказуемым.

![](https://www.smbc-comics.com/comics/20130605.png)

Раньше для решения этого вопроса стандартным методом было хранение денег в копейках в целочисленных типах,
например `integer`, и я долгое время считал это единственным верным решением.

Но с `int`ом всё не так однозначно, он определённо лучше, чем `float`, но и свои недостатки имеет.
Например, чтобы получить проценты, нужно всё умножать и делить, а после деления мы уже почти наверняка
получим `float` и вполне возможно дробное количество копеек.

И при работе с `int` нужно будет каждый раз решать вопрос - что делать с дробными копейками?

Каждый разработчик будет решать это по-своему - кто-то через `round`, кто-то через `floor`, кто-то через `ceil`,
а кто-то даже не задумается об этом и сразу передаст `float` в `number_format`.

Это наглядно видно [в тесте](https://github.com/shanginn/laravel-decimal-tutorial/blob/05843e92aad506c85859c311b5e3f431a3c1e19a/tests/Feature/ProductControllerTest.php#L44)
где мы считаем НДС "по наитию" через `$копейки * $НДС / 100` и приводим к строке через `number_format`,
а потом сравниваем результат с подсчетом через fmod

На мой взгляд лучше определиться один раз, решить эту проблему в одном месте и покрыть тестами,
чтобы при изменении бизнес-требований нужно было поменять только там.

Поэтому мы не хотим `float`, мы не хотим `int`, мы хотим - `decimal`! - 
число с точкой, но не плавающей, а фиксированной.
Он решает проблему с точностью, но создаёт новую - в PHP нет типа `decimal`, и обычно он представляется строкой.

Для решения уже этой проблемы я предлагаю сделать класс `Money` для работы с деньгами в формате `decimal`.

> Я знаю, что уже есть готовые решения, например
> [MoneyPHP](https://github.com/moneyphp/money), или
> [Brick\Money](https://github.com/brick/money), или
> [Laravel Money](https://github.com/cknow/laravel-money),
> но я хочу показать, как это можно сделать самостоятельно,
> чтобы лучше понять, как это работает.

## Что хотим?
- Хранить деньги в базе с типом `decimal`
- На бэкэнде иметь возможность работать как с числом
- Отдавать на фронтенд число строкой
- Получать с фронтенда число из строки

## Что получилось?
### База
Для создания поля с типом `decimal` в базе данных в Laravel есть тип `decimal` в миграциях:
[пример миграции](https://github.com/shanginn/laravel-decimal-tutorial/blob/05843e92aad506c85859c311b5e3f431a3c1e19a/database/migrations/2023_07_20_071434_create_products_table.php#L19)

В PostgreSQL это будет [`NUMERIC(8, 2)`](https://www.postgresql.org/docs/current/datatype-numeric.html),
в MySQL - [`DECIMAL(8, 2)`](https://dev.mysql.com/doc/refman/8.0/en/fixed-point-types.html).

### Бэкэнд
Для работы с деньгами мы делаем класс 
[`Money`](https://github.com/shanginn/laravel-decimal-tutorial/blob/master/app/DataObjects/Money.php)
со всеми математическими операциями и конвертацией в строку и из строки.

Самое сложное в этом классе - 
[операции деления](https://github.com/shanginn/laravel-decimal-tutorial/blob/05843e92aad506c85859c311b5e3f431a3c1e19a/app/DataObjects/Money.php#L55) 
(в том числе проценты), потому что я понятия не имею, как правильно решать вопрос с дробными копейками.
Мой подход - округлять в нижнюю сторону до ближайшей целой копейки
и возвращать результат вместе с этим дробным остатком, а вы уже сами решайте, что с ним делать.

Вам нужна другая логика? Здорово! Вы знаете, где это поменять.

Чтобы в модели работать с `Money`, а не строкой используем
[`$casts`](https://github.com/shanginn/laravel-decimal-tutorial/blob/05843e92aad506c85859c311b5e3f431a3c1e19a/app/Models/Product.php#L22),
который понимает как нужно конвертировать через
[кастомный каст](https://github.com/shanginn/laravel-decimal-tutorial/blob/master/app/Casts/Money.php) 
([документация](https://laravel.com/docs/10.x/eloquent-mutators#custom-casts)).

### Из фронтенда
В [запросе на создание](https://github.com/shanginn/laravel-decimal-tutorial/blob/master/app/Http/Requests/StoreProductRequest.php)
используем дефолтную валидация через `decimal:2`, но можно сделать и [кастомную](https://laravel.com/docs/10.x/validation#custom-validation-rules).

Чтобы из запроса мы сразу работали с `Money`, после прохождения валидации
конвертируем сумму внутри (`passedValidation`)[https://github.com/shanginn/laravel-decimal-tutorial/blob/05843e92aad506c85859c311b5e3f431a3c1e19a/app/Http/Requests/StoreProductRequest.php#L24C24-L24C40]

### На фронтенд
Чтобы на фронт приходила строка, нужна
[сериализация](https://github.com/shanginn/laravel-decimal-tutorial/blob/05843e92aad506c85859c311b5e3f431a3c1e19a/app/Casts/Money.php#L48)


### Тесты
Класс по работе с деньгами покрываем
[Unit-тестами](https://github.com/shanginn/laravel-decimal-tutorial/blob/master/tests/Unit/MoneyTest.php).

А создание и получение - [функциональными](https://github.com/shanginn/laravel-decimal-tutorial/blob/master/tests/Feature/ProductControllerTest.php).

## Заключение
Вот и всё. Получилось достаточно интересное упражнение.
