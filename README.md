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
- Why Factory it automatically creates the fields as much as many ,what if manually want tocreate ten fields like name?

### How to use Factory?

- To create `TodoList::factory()->create();`
- After adding this run the test you may face the below  error. 
```php
Tests\Feature\TodoListTest > fetch todo list
   PHPUnit\Framework\ExceptionWrapper 

  Class "Database\Factories\TodoListFactory" not found


```
- Write the factory?
`php artisan make:factory TodoListFactory -h means help.`
- add attach with model  `php artisan make:factory TodoListFactory -m Todolist`
- Lets define the attribute fields in TodoListFactory.php file.
- Basically we are just filling the field that we need for table column.
```php


<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todolist>
 */
class TodoListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'name' => 'my List',
        ];
    }
}


```
- Now run the test you will get passed.
```php


 php artisan test
Warning: TTY mode is not supported on Windows platform.

   PASS  Tests\Feature\TodoListTest
  ✓ fetch todo list

  Tests:  1 passed
  Time:   2.36s
```
How the factory creates and test passed? lets see through die and dumb.
- lets die and dumb `dd($list);`

```php
 php artisan test
Warning: TTY mode is not supported on Windows platform.
App\Models\TodoList {#1229 // tests\Feature\TodoListTest.php:18
  #connection: "sqlite"
  #table: null
  #primaryKey: "id"
  #keyType: "int"
  +incrementing: true
  #with: []
  #withCount: []
  +preventsLazyLoading: false
  #perPage: 15
  +exists: true
  +wasRecentlyCreated: true
  #escapeWhenCastingToString: false
  #attributes: array:4 [
    "name" => "my List"
    "updated_at" => "2024-09-04 07:08:40"
    "created_at" => "2024-09-04 07:08:40"
    "id" => 1
  ]
  #original: array:4 [
    "name" => "my List"
    "updated_at" => "2024-09-04 07:08:40"
    "created_at" => "2024-09-04 07:08:40"
    "id" => 1
  ]
  #changes: []
  #casts: []
  #classCastCache: []
  #attributeCastCache: []
  #dates: []
  #dateFormat: null
  #appends: []
  #dispatchesEvents: []
  #observables: []
  #relations: []
  #touches: []
  +timestamps: true
  #hidden: []
  #visible: []
  #fillable: array:1 [
    0 => "name"
  ]
  #guarded: array:1 [
    0 => "*"
  ]
}



```
- Factory is great but we have another great thing called Faker.
- Faker is a Fake Generation Library.
- Faker is a base class of Factory.

```php

class TodoListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            //'name' => 'my List',
            'name'=> $this->faker->sentence//or->'name'=> $this->faker->name;
        ];
    }
}





```
- Above faker will generate what you have requested eg. name or sentence.
- after dd using faker my results below

```php

 #attributes: array:4 [
    "name" => "Placeat quidem sequi quas aperiam odio excepturi doloribus."
    "updated_at" => "2024-09-04 12:08:59"
    "created_at" => "2024-09-04 12:08:59"
    "id" => 1
  ]
  #original: array:4 [
    "name" => "Placeat quidem sequi quas aperiam odio excepturi doloribus."
    "updated_at" => "2024-09-04 12:08:59"
    "created_at" => "2024-09-04 12:08:59"
    "id" => 1
  ]

  ```
- How to override the name that facker generated
`  $list = TodoList::factory()->create(['name' => 'my list']);`
