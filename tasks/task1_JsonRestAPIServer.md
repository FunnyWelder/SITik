### Лабораторная работа №1. 
Реализация простого Rest API с использованием фреймворка (2 часа).

#### Цель:
Получить практические навыки работы реализации Rest API.

#### Теоретическая часть:
+ https://www.cleverence.ru/articles/elektronnaya-kommertsiya/rest-api-chto-eto-takoe-prostymi-slovami-primery-zaprosov-varianty-ispolzovaniya-servisa-metody/

#### Постановка задачи: 
Реализовать простое web-приложение, позволяющее регистрироваться в системе и управлять личным списком задач (TODO-лист) посредством простого Rest API (json). 

#### Список ручек: 
Регистрация пользователя - POST /user, получить список задач пользователя - GET /todo (если пользователя не существует, то возвращать 4XX код ошибки), добавить задачу пользователя - POST /todo, удалить задачу пользователя - DELETE /todo/{id}, обновить задачу пользователя - PUT /todo/{id}. Аутентификация пользователя с использованием HTTP Basic Authentication или JWT.

#### Порядок выполнения:
+ Анализ задачи
+ Исследование источников
+ Установить и настроить web-сервер Nginx
+ Установить и настроить Symfony + Doctrine (или иную связку на другом языке)
+ Реализовать простую регистрацию пользователя
+ Проверить API, используя curl/Postman
+ Реализовать аутентификацию пользователя с помощью HTTP Basic Authentication или JWT.
+ Проверить API, используя curl/Postman
+ Реализовать ручки управления задачами
+ Проверить API, используя curl/Postman

#### Форма отчета: 
Репозиторий на GitHub, с исходным кодом полученного web-приложения и скриншотами проверки API с помощью curl/Postman