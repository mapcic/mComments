# mComments
Индексируемая система комментариев. Код стоит из двух частей:
- Клиентская часть *client/*:
	* *index.html* -- HTML структура комментариев;
	* *index.php* -- PHP функция для вывода **индексируемых** комментариев
	* *mComments.js* -- JS функции для добавления, подгрузки комментариев;
	* *mCommentsAdd.php* -- скрипт добавления новых комментариев; 
	* *mCommentsMore.php* -- скрипт загрузки новых комментариев; 
	* *style.css* -- пример оформления;
	* *example.php* -- пример кода для вставки в Joomla.
- Админская часть *admin/*:
	* *index.html* -- HTML структура комментариев;
	* *init.sql* -- SQL инициализации таблиц;
	* *init.php* -- инициализация таблиц;
	* *getOptions.php* -- выведение списка *страница -> таблица*;
	* *mCommentsAdmin.js* -- скрипт управления админкой;
	* *mCommentsAdmin.php* -- взаимодействие с бд;
	* *style.css* -- пример оформления;
	* *example.php* -- пример кода для вставки в Joomla.
## Установка
Рекомендуемый последовательность установки:
1. Добавление стандартных стилей из *admin/*, *client/*;
2. Добавление части администратора;
3. Пробный запуск;
4. Добавление клиентской части;
5. Проверка;
6. Изменение стилей.

Для вставки кода в материалы *Joomla* используется плагин *sourcerer*.
### Добавление стандартных стилей
Добавьте содержимое *style.css* из *admin/* или *client/* в ваш шаблон. Например: */templates/protostar/css/template.css* или создайте новый *myStyle.css* и подгружайте его в шапке.
```html
<head>
...
<link rel="stylesheet" type="text/css" href="/path/to/myStyle.css">
...
</head>
```
### Добавление части администратора
Создайте новый модуль типа *HTML-код* в **Расширения &rarr; Модули &rarr; Создать**. В поле **"Заголовок"** наберите "mCommentsAdmin". Содержимое этого модуля можно скопировать из файла *admin/example.php* или собрать по частям из:
1. *index.html*;
2. *init.php*;
3. *getOptions.php*;
4. *mCommentsAdmin.js*.

Если вы добавили содержимое файла *example.php*, загрузите на сервер файлы *mCommentsAdmin.js* и *mCommentsAdmin.php*.

В файле *mCommentsAdmin.php* необходимо поправить количество подкаталогов от корня сайта:
```php
...
define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){<<Цифра>>}$/', '', dirname(__FILE__)));
...
```
Например, если *mCommentsAdmin.php* расположен в каталоге */templates/myTemplate/php/* -- вместо <<Цифра>> необходимо вставить число **3**.

В файле *mCommentsAdmin.js* измените путь до файла *mCommentsAdmin.php* на реальный:
```javascript
...
url : '/path/to/mCommentsAdmin.php',
...
```

В файле *example.php* необходимо прописать путь к *mCommentsAdmin.js*:
```html
...
<script type="text/javascript" onload="mComments();" src="/templates/protostar/js/mCommentsAdmin.js" defer></script>
...
```

**"Опубликуйте"** созданный модуль, а **"Доступ"** выставите в **"Registered"**.

### Пробный запуск
Выставите полученный выше модуль на страницу. Зайдите на нее. Авторизуйтесь. Вы только что создали базу комментариев.

### Добавление клиентской части
Создайте новый модуль типа *HTML-код* в **Расширения &rarr; Модули &rarr; Создать**. В поле **"Заголовок"** наберите "mComments". Содержимое этого модуля можно скопировать из файла *client/example.php* или собрать по частям из:
1. *index.html*;
2. *index.php*;
3. *getOptions.php*;
4. *mComments.js*.

Если вы добавили содержимое файла *example.php*, загрузите на сервер файлы *mComments.js*, *mCommentsAdd.php*, *mCommentsMore.php*.

В файле *mComments.js* измените путь до *mCommentsAdd.php* и "mCommentsMore.php":
```javascript
...
type : 'POST', url : '/path/to/mCommentsAdd.php',
...
type : 'POST', url : '/path/to/mCommentsMore.php',
...
```

В файле *example.php* измените путь до *mComments.js* на фактический:
```html
...
<script onload="jQuery(document).ready(function(){mComments();});" type="text/javascript" src="path/to/mComments.js"></script>
...
```
В "mCommentsAdd.php" и "mCommentsMore.php" замените количество подкаталогов, как описано ранее:
```php
...
define('JPATH_BASE', preg_replace('/(?:\/[\w\-]+){<<ЦИФРА>>}$/', '', dirname(__FILE__)));
...
```
**"Опубликуйте"** созданный модуль, а **"Доступ"** выставите в **"Public"**.

### Проверка
Для вставки комментариев необходимо зайти в редактирование материала и добавить наш модуль "mCommetns".

### Изменение стилей
Изменяйте и добавляйте любые классы, кроме *.ShliamburOff* -- он отвечает за отображение и сокрытие элементов.