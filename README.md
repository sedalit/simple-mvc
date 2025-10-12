# Simple MVC Framework

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/sedalit/simple-mvc-framework)](https://packagist.org/packages/sedalit/simple-mvc-framework)
[![Downloads](https://img.shields.io/packagist/dt/sedalit/simple-mvc-framework)](https://packagist.org/packages/sedalit/simple-mvc-framework)

Легковесный PHP MVC фреймворк для быстрой разработки веб-приложений, который был создан в учебно-познавательных целях. Создан с акцентом на простоту, производительность и современные практики разработки.

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

## 🚀 Установка

### Создайте новый проект

```bash
composer create-project sedalit/simple-php-framework-skeleton my-app
cd my-app
```

### Настройте .env

```bash
cp .env.example .env
# Edit .env with your database credentials
```

### Запустите dev-сервер

```bash
php -S localhost:8000 -t public
```

Откройте `http://localhost:8000` в браузере.

## 📖 Документация

### Маршруты

Определите маршруты в `config/routes.php`:

```php
use App\Controllers\PostController;

// Базовые маршруты
$app->router()->get('/', [IndexController::class, 'index']);
$app->router()->post('/posts', [PostController::class, 'store']);

// Маршрут с динамическими параметрами
$app->router()->get('/posts/(?<id>\d+)', [PostController::class, 'show']);

// Маршрут с middleware
$app->router()->get('/dashboard', [DashboardController::class, 'index'], [
    AuthMiddleware::class
]);

// Группа маршрутов
$app->router()->group('/admin', [
    $app->router()->get('/users', [AdminController::class, 'users']),
    $app->router()->get('/settings', [AdminController::class, 'settings']),
])->middleware([AuthMiddleware::class]);
```

### Контроллеры

```php
namespace App\Controllers;

class PostController extends BaseController 
{
    public function index(): mixed
    {
        $posts = db()->findAll('posts');
        
        return $this->render('posts/index', ['posts' => $posts]);
    }
    
    public function show(): mixed
    {
        $id = router()->routeParam('id');
        $post = db()->findOrFail('posts', $id);
        
        return $this->render('posts/show', ['post' => $post]);
    }
}
```

### Модели

```php
namespace App\Models;

use PHPFramework\Model;

class Post extends Model 
{
    protected array $fillable = ['title', 'content', 'user_id'];
    
    protected function tableName(): string 
    {
        return 'posts';
    }
    
    protected function primaryKeyName(): string 
    {
        return 'id';
    }
}

// Использование
$post = new Post();
$post->title = 'My Post';
$post->content = 'Content here';
$id = $post->save();

$post->update();
$post->delete($id);
```

### Валидация

```php
$data = request()->post();

$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
    'password_confirm' => 'required|match:password',
    'username' => 'required|min:3|max:20|unique:users',
    'avatar' => 'file|extension:jpg,png|fileSize:2MB'
];

if (!validate($data, $rules)) {
    $errors = validator()->errors();
    return $this->render('form', ['errors' => $errors]);
}
```

### Представления

```php
// В контроллере
return $this->render('posts/index', [
    'title' => 'All Posts',
    'posts' => $posts
]);

// В представлении (app/Views/posts/index.php)
<h1><?= h($title) ?></h1>

<?php foreach($posts as $post): ?>
    <article>
        <h2><?= h($post['title']) ?></h2>
        <p><?= h($post['content']) ?></p>
    </article>
<?php endforeach; ?>
```

### Middleware

```php
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

### Сессии

```php
// Установка значение
session()->set('user_id', 123);

// Получение значения
$userId = session()->get('user_id');

// Flash-сообщения
session()->setFlash('success', 'Post created!');
$message = session()->getFlash('success');

// Проверка наличия
if (session()->has('user_id')) {
    // User is logged in
}
```

### Кэш

```php
// Сохранить в кэш со сроком жизни в 1 час
cache()->set('popular_posts', $posts, 3600);

// Получить из кэша
$posts = cache()->get('popular_posts', []);

// Удалить из кэша
cache()->forget('popular_posts');
```

### Почта

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

## 🎨 CLI команды

```bash
# Генерация файлов
php bin/console make:controller PostController
php bin/console make:model Post
php bin/console make:middleware AdminMiddleware
php bin/console make:rule PhoneRule

# Управление приложением
php bin/console cache:clear
php bin/console app:setup

# Помощь
php bin/console help
```

## 🔧 Функции-хэлперы

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

// Кэш
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

## 📚 Правила валидации

- `required` - Field is required
- `email` - Valid email address
- `min:n` - Minimum length
- `max:n` - Maximum length
- `match:field` - Match another field
- `unique:table` - Unique in database table
- `file` - File uploaded successfully
- `fileSize:size` - File size limit (e.g., `2MB`)
- `extension:ext1,ext2` - Allowed file extensions

## 🏗️ Структура

```
Framework Structure (vendor/sedalit/simple-mvc-framework/src/)
├── Application.php          # Application core
├── Router.php              # Route dispatcher
├── Database.php            # Database wrapper
├── QueryBuilder.php        # Fluent query builder
├── Controller.php          # Base controller
├── Model.php              # Base model with ActiveRecord
├── View.php               # View renderer
├── Request.php            # HTTP request
├── Response.php           # HTTP response
├── Session.php            # Session management
├── Cache.php              # File cache
├── Pagination.php         # Pagination logic
├── ServiceContainer.php   # DI container
├── Interfaces/            # Interfaces
├── Middlewares/           # Built-in middleware
├── Routing/              # Routing components
├── Security/             # Security features
├── Services/             # Framework services
├── Utils/                # Utility classes
├── Validation/           # Validation system
└── helpers.php           # Helper functions
```

## 🧪 Тестирование

```bash
composer test
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

## 📖 Связанные проекты

- [simple-php-framework-skeleton](https://github.com/sedalit/simple-php-framework-skeleton) - Скелет приложения для быстрого старта