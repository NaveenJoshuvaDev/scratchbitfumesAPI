<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Learn API from Scratch

1. Every Test file should have a suffix test.php for example ExampleTest.php

```php


 <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <!-- <env name="DB_CONNECTION" value="sqlite"/> -->
        <!-- <env name="DB_DATABASE" value=":memory:"/> -->
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>

```
2. We are running Test inside the SQLlite in memory so uncomment that.

```php


 <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/> 
        <env name="DB_DATABASE" value=":memory:"/> 
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>

```

***TEST FILE To check our Homepage***
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');//homepagesss

        $response->assertStatus(200);
    }
}


```
`php artisan  test`
- Our first Test Results
```php
  PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

  Tests:  2 passed
  Time:   0.21s


```

Where we are going to write the API?
write it in API route.

But before creating API we are here to first write an Test

```php
php artisan make:test TodoListTest
```

Every Test Has 3 phase

- Preparation
- Action /perform
- Assertion/predict

- we have created an route for testing purposes.`Route::get('todo-list', [TodoListController::class, 'index']);`
Errors
- NotfoundhttpExecution ,because of API has prefix than ,webroutes
```php
public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }
```
- next error `Class "TodoListController" does not exist `
- Lets write that controller
- using this cmd it will generate functions for API too
```php

php artisan make:controller TodoListController --api
```
- now below error
```php

BadMethodCallException: Method App\Http\Controllers\TodoListController::index does not exist.

```


- if you want to show real errors without the handling of exception handling use `testcase.php`.
### Fetch from Database.
- 1st you have to create a database so create a model named TodoList
```php

php artisan make:model TodoList

```
- now we didn't migrate database so we get after running test
errors
- PdoException
- QueryException
- Lets create a migration file first for creating tables in Database.
`php artisan make:migration CreateTodoListsTable`
- now if we run the test we might see the same result error why because we need migrating the database for in memory database 
- So add a trait called Refresh Database.
- It is used to migrate and remigrate a database for testing Purpose.
```php

class TodoListTest extends TestCase
{
    use RefreshDatabase;
    public function test_fetch_todo_list()
    {
    }
```
- Now the test Results shows
```php

• Tests\Feature\TodoListTest > fetch todo list
  Failed asserting that 0 matches expected 1.

  ```
- we are getting 0 results but expecting 1.
- why we get 0 because there is no data in the database.
- so lets insert Data.

```php

 public function test_fetch_todo_list()
    {
        
        //preparation/prepare
        TodoList::create(['name' => 'my list']);
       
      $response = $this->getJson(route('todolist.store'));
      
    $response->assertStatus(200);
    //dd($response->json());
       $this->assertEquals(1, count($response->json()));
        //assert means getting or fetching the received data.


    }
```
 
- we get error `Add [name] to fillable property to allow mass assignment on [App\Models\TodoList]`.
- whenever while inserting data you must add Filllable property Mass Assignment in the Model.
```php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

   protected $fillable = ['name'];
}

```
- we get another error after the test
```php
QueryException



  SQLSTATE[HY000]: General error: 1 table todo_lists has no column named name (SQL: insert into "todo_lists" ("name", "updated_at", "created_at") values (my list, 2024-09-04 06:00:04, 2024-09-04 06:00:04))
```

- Telling that table has no column name like name ,so we have to add that in the table 
- Here Table means Migration file.


```php

public function up()
    {
        Schema::create('todo_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
```

Now thte test of inserting data passed.

```php


 php artisan test
Warning: TTY mode is not supported on Windows platform.

   PASS  Tests\Feature\TodoListTest
  ✓ fetch todo list

  Tests:  1 passed
  Time:   0.27s
```
- For creating or inserting data we used this ` TodoList::create(['name' => 'my list']);` 
- But using Factory makes efficient.
