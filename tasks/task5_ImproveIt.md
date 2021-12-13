## Лабораторная работа №5.
Использование Case-инструментов для улучшения качества ПО. (2 часа).

#### Цель:
Получить практические навыки работы с case-инструментами:  phpstan, phpcs, phpcbf, php-cs-fixer, phpmd, ecs.

#### Теоретическая часть:
+ https://phpstan.org/
+ https://github.com/squizlabs/PHP_CodeSniffer
+ https://github.com/FriendsOfPHP/PHP-CS-Fixer
+ https://phpmd.org/
+ https://github.com/symplify/easy-coding-standard

#### Постановка задачи:
Установить case-инструменты: phpstan, phpcs, phpcbf, php-cs-fixer, phpmd, ecs, в ранее реализованном проекте и с их помощью улучшить качество разрабатываемого ПО.

#### Порядок выполнения:
+ Анализ задачи
+ Исследование источников
+ Установить case-инструменты в проект:
  + phpstan - https://phpstan.org/
  + phpcs, phpcbf - https://github.com/squizlabs/PHP_CodeSniffer
  + php-cs-fixer - https://github.com/FriendsOfPHP/PHP-CS-Fixer
  + phpmd - https://phpmd.org/
  + ecs - https://github.com/symplify/easy-coding-standard
+ Запустить каждый инструмент из каталога vendor/bin и получить информацию о найденных в проекте ошибках. Зафиксировать найденные ошибки и их количество, чтобы можно было определить масштабы работы по исправлению.
+ Исправить ошибки или понизить строгость инструментов.
+ Зафиксировать отсутствие ошибок
  
#### Форма отчета: 
Репозиторий на GitHub, с исходным кодом проект и скриншотами работы case-инструментов до/после исправлений.