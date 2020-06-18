# Laravel Action

[![Latest Stable Version](https://poser.pugx.org/laraditz/action/v/stable?format=flat-square)](https://packagist.org/packages/laraditz/action)
[![Total Downloads](https://img.shields.io/packagist/dt/laraditz/action?style=flat-square)](https://packagist.org/packages/laraditz/action)
[![License](https://poser.pugx.org/laraditz/action/license?format=flat-square)](https://packagist.org/packages/laraditz/action)
[![StyleCI](https://github.styleci.io/repos/7548986/shield?style=square)](https://github.com/laraditz/action)

Single action class for Laravel and Lumen to keep your application DRY.

## Installation

Via Composer

```bash
$ composer require laraditz/action
```

## Configuration

The Laravel and Lumen configurations vary slightly, so here are the instructions for each of the frameworks.

### Laravel

Edit the `config/app.php` file and add the following line to register the service provider:

```php
'providers' => [
    ...
    Laraditz\Action\ActionServiceProvider::class,
    ...
],
```

> Tip: If you're on Laravel version **5.5** or higher, you can skip this part of the setup in favour of the Auto-Discovery feature.

### Lumen

Edit the `bootstrap/app.php` file and add the following line to register the service provider:

```php
...
$app->register(Laraditz\Action\ActionServiceProvider::class);
...
```

## Usage

You can use `php artisan make:action <name>` to create your action. For example, `php artisan make:action CreateNewPost`. By default you can find it in `App/Actions` folder. 

Sample action file generated with some logic added as below:
```php
namespace App\Actions;

use Laraditz\Action\Action;

class CreateNewPost extends Action
{
    // Optional
    public function rules()
    {
        return [
            'title' => 'required',
            'body' => 'required|min:10',
        ];
    }

    public function handle()
    {
        // Your logic goes here
        \App\Post::create($this->validated());

        // use $this->validated() to get all validated attributes based on rules.
        // You also can use $this->all() to retreive all attributes passed if there is no rules.
    }
}
```
> You can totally remove the `rules` method if you are not using it or just leave it as is.

Now that you've created your action, you can call it in few ways as below:

**Using plain object**
```php
$createNewPost = new CreateNewPost([
    'title' => 'My first post', 
    'body' => 'This is a post content'
]);

$createNewPost->now();
```

**Using static method**
```php
CreateNewPost::now([
    'title' => 'My first post', 
    'body' => 'This is a post content'
]);
```

**Using invokable**
```php
// routes/web.php
Route::post('posts', '\App\Actions\CreateNewPost');
```

## Credits

- [Raditz Farhan](https://github.com/raditzfarhan)

## License

MIT. Please see the [license file](LICENSE) for more information.
