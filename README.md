# Simple MVC Framework

Легковесный PHP MVC фреймворк для быстрой разработки веб-приложений, который был создан в учебно-познавательных целях. Создан с акцентом на простоту, производительность и современные практики разработки.

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## 🚀 Особенности

- **MVC Architecture** - Чистая архитектура Model-View-Controller
- **Роутинг** - Гибкая система маршрутизации с поддержкой параметров и групп
- **Middleware** - Встроенная поддержка middleware (Auth, Guest, CSRF)
- **Валидация** - Расширяемая система валидации форм
- **Database** - PDO wrapper с query builder
- **View Engine** - Простой и быстрый шаблонизатор
- **Session Management** - Управление сессиями и flash-сообщениями
- **Cache** - Файловая система кеширования
- **Mail Service** - Отправка email через PHPMailer
- **CSRF Protection** - Встроенная защита от CSRF атак
- **Service Container** - Dependency Injection контейнер
- **Environment Variables** - Поддержка .env файлов
- **Pagination** - Готовая система пагинации
- **File Upload** - Удобная работа с загрузкой файлов

## 📋 Требования

- PHP >= 8.0
- PDO Extension
- Composer
- Apache/Nginx с mod_rewrite

## 📦 Установка

### 1. Клонируйте репозиторий

```bash
git clone https://github.com/sedalit/simple-mvc-framework.git
cd simple-mvc-framework
```

### 2. Установите зависимости

```bash
composer install
```

### 3. Настройте окружение

Скопируйте `.env.example` в `.env` и настройте параметры:

```bash
cp .env.example .env
```

Отредактируйте `.env`:

```env
# DATABASE
DB_HOST="localhost"
DB_NAME="your_database"
DB_USERNAME="your_username"
DB_PASSWORD="your_password"
DB_CHARSET="utf8mb4"

# MAIL
MAIL_HOST="smtp.example.com"
MAIL_USERNAME="your@email.com"
MAIL_PASSWORD="your_password"
MAIL_PORT="465"
MAIL_SMTP_AUTH=true
MAIL_SMTP_SECURE='ssl'
```

### 4. Настройте веб-сервер

Укажите `public` как корневую директорию. Пример для Apache включен в `.htaccess`.

### 5. Настройте приложение

Отредактируйте `config/init.php`:

```php
const APP_NAME = 'Your App Name';
const PATH = 'http://your-domain.local';
const DEBUG = 1; // 0 для production
```

## 🎯 Быстрый старт

### Создание контроллера

```php
<?php

namespace App\Controllers;

class PostController extends BaseController 
{
    public function index() : mixed
    {
        $posts = db()->findAll('posts');
        
        return $this->render('posts/index', [
            'posts' => $posts
        ]);
    }
    
    public function show() : mixed
    {
        $id = router()->routeParam('id');
        $post = db()->findOrFail('posts', $id);
        
        return $this->render('posts/show', [
            'post' => $post
        ]);
    }
}
```

### Определение маршрутов

В файле `config/routes.php`:

```php
<?php

use App\Controllers\PostController;

// Простые маршруты
$app->router()->get('/', [IndexController::class, 'index']);
$app->router()->get('/posts', [PostController::class, 'index']);
$app->router()->get('/posts/(?<id>\d+)', [PostController::class, 'show']);

// Маршруты с middleware
$app->router()->post('/posts', [PostController::class, 'store'], [
    AuthMiddleware::class
]);

// Группы маршрутов
$app->router()->group('/admin', [
    $app->router()->get('/dashboard', [AdminController::class, 'index']),
    $app->router()->get('/users', [AdminController::class, 'users']),
])->middleware([AuthMiddleware::class]);
```

### Создание модели

```php
<?php

namespace App\Models;

use PHPFramework\Model;

class Post extends Model 
{
    protected array $fillable = ['title', 'content', 'author_id'];
    
    protected function tableName(): string 
    {
        return 'posts';
    }
    
    protected function primaryKeyName(): string 
    {
        return 'id';
    }
}
```

### Работа с моделью

```php
// Создание
$post = new Post();
$post->title = 'My Post';
$post->content = 'Content here';
$id = $post->save();

// Обновление
$post = new Post();
$post->id = 1;
$post->title = 'Updated Title';
$post->update();

// Удаление
$post->delete(1);
```

### Создание представления

`app/Views/posts/index.php`:

```php
<div class="container">
    <h1>Posts</h1>
    
    <?php foreach($posts as $post): ?>
        <article>
            <h2><?= h($post['title']) ?></h2>
            <p><?= h($post['content']) ?></p>
        </article>
    <?php endforeach; ?>
</div>
```

### Валидация форм

```php
public function store() : mixed
{
    $data = request()->post();
    
    $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
        'password_confirm' => 'required|match:password',
        'username' => 'required|min:3|max:20|unique:users'
    ];
    
    if (!validate($data, $rules)) {
        $errors = validator()->errors();
        return $this->render('form', ['errors' => $errors]);
    }
    
    // Обработка данных
}
```

### Работа с сессиями

```php
// Установка значения
session()->set('user_id', 123);

// Получение значения
$userId = session()->get('user_id');

// Flash сообщения
session()->setFlash('success', 'Post created successfully!');
$message = session()->getFlash('success');

// Проверка существования
if (session()->has('user_id')) {
    // ...
}
```

### Кеширование

```php
// Сохранение в кеш на 1 час
cache()->set('popular_posts', $posts, 3600);

// Получение из кеша
$posts = cache()->get('popular_posts', []);

// Удаление из кеша
cache()->forget('popular_posts');
```

### Отправка email

```php
use PHPFramework\Services\Mail\Mail;

$mail = new Mail(
    from: 'noreply@example.com',
    subject: 'Welcome!',
    body: 'Welcome to our platform',
    to: ['user@example.com']
);

App::mailer()->send($mail);
```

## 🛡️ Middleware

Фреймворк поддерживает middleware для обработки запросов:

### Встроенные Middleware

- **AuthMiddleware** - Проверка авторизации пользователя
- **GuestMiddleware** - Доступ только для неавторизованных
- **CsrfMiddleware** - Защита от CSRF атак (автоматически для POST/PUT/DELETE)

### Создание собственного Middleware

```php
<?php

namespace App\Middlewares;

use PHPFramework\Interfaces\MiddlewareInterface;
use PHPFramework\Request;
use PHPFramework\Response;

class AdminMiddleware implements MiddlewareInterface 
{
    public function handle(Request $request, Response $response, callable $next): mixed
    {
        if (!isAdmin()) {
            abort('Access denied', 403);
        }
        
        return $next();
    }
}
```

## 📚 Валидация

### Встроенные правила валидации

- `required` - Обязательное поле
- `email` - Email адрес
- `min:n` - Минимальная длина
- `max:n` - Максимальная длина
- `match:field` - Совпадение с другим полем
- `unique:table` - Уникальность в таблице
- `file` - Файл загружен без ошибок
- `fileSize:size` - Размер файла (например, `2MB`)
- `extension:ext1,ext2` - Допустимые расширения

### Создание собственного правила

```php
<?php

namespace App\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class PhoneRule extends ValidationRule 
{
    protected string $message = "The :fieldname: must be a valid phone number";
    
    public static function key(): ?string 
    {
        return 'phone';
    }
    
    public function passes(): bool 
    {
        return preg_match('/^\+?[1-9]\d{10,14}$/', $this->value);
    }
}
```

## 🗃️ База данных

### Прямые запросы

```php
// SELECT
$users = db()->query("SELECT * FROM users WHERE status = ?", ['active'])->getAll();

// INSERT
db()->query("INSERT INTO users (name, email) VALUES (?, ?)", ['John', 'john@example.com']);
$id = db()->getInsertedId();

// UPDATE
db()->query("UPDATE users SET name = ? WHERE id = ?", ['Jane', 1]);
$affected = db()->rowCount();

// DELETE
db()->query("DELETE FROM users WHERE id = ?", [1]);
```

### Готовые методы

```php
// Получить все записи
$users = db()->findAll('users');

// Получить одну запись
$user = db()->findOne('users', 1);

// Получить или выбросить 404
$user = db()->findOrFail('users', 1);

// Подсчет записей
$count = db()->count('users');
```

## 🔧 Helper функции

Фреймворк предоставляет удобные helper функции:

```php
// Приложение
app() // Экземпляр Application

// View
view('template', $data, $layout) // Рендеринг view

// Request/Response
request() // Объект Request
response() // Объект Response
redirect('/path') // Редирект

// Валидация
validator() // Объект Validator
validate($data, $rules) // Быстрая валидация

// База данных
db() // Объект Database

// Сессия
session() // Объект Session
checkAuth() // Проверка авторизации

// Кеш
cache() // Объект Cache

// URL
baseUrl('/path') // Базовый URL приложения

// Безопасность
h($string) // htmlspecialchars
csrf() // CSRF token input

// Environment
env('KEY', 'default') // Получить переменную окружения

// Прочее
abort($message, $code) // Прервать выполнение с ошибкой
old('field') // Старое значение поля
formErrors('field', $errors) // Вывод ошибок валидации
```

## 📄 Структура проекта

```
simple-mvc-framework/
├── app/
│   ├── Controllers/        # Контроллеры приложения
│   ├── Models/            # Модели данных
│   ├── Views/             # Представления
│   │   ├── layouts/       # Шаблоны макетов
│   │   └── ...
│   └── Validation/
│       └── Rules/         # Пользовательские правила валидации
├── config/
│   ├── init.php           # Основная конфигурация
│   ├── db.php             # Конфигурация БД
│   ├── mail.php           # Конфигурация почты
│   ├── routes.php         # Определение маршрутов
│   └── serviceProviders.php # Сервис-провайдеры
├── core/                  # Ядро фреймворка
│   ├── Application.php
│   ├── Router.php
│   ├── Database.php
│   └── ...
├── helpers/
│   └── functions.php      # Helper функции
├── public/                # Публичная директория
│   ├── index.php          # Точка входа
│   ├── .htaccess
│   └── assets/            # CSS, JS, изображения
├── tmp/
│   └── cache/             # Файлы кеша
├── uploads/               # Загруженные файлы
├── vendor/                # Composer зависимости
├── .env.example           # Пример environment файла
└── composer.json
```

## 🧪 Тестирование

Фреймворк настроен для работы с PHPUnit:

```bash
composer require --dev phpunit/phpunit
./vendor/bin/phpunit tests/
```

## 🤝 Вклад в разработку

Contributions, issues и feature requests приветствуются!

1. Fork проекта
2. Создайте feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit изменения (`git commit -m 'Add some AmazingFeature'`)
4. Push в branch (`git push origin feature/AmazingFeature`)
5. Откройте Pull Request

## 👤 Автор

**Vladislav Agarkov**
- Email: vlad.agarkov@alto-ai.ru
- GitHub: [@sedalit](https://github.com/sedalit)

## 🌟 Поддержка проекта

Если проект оказался полезным, поставьте ⭐️!