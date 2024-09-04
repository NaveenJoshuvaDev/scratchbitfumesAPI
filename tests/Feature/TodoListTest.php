<?php

namespace Tests\Feature;

use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;
    public function test_fetch_todo_list()
    {
        //$this->withoutExceptionHandling();
        //preparation/prepare
        $list = TodoList::factory()->create();
       // dd($list);
        //TodoList::create(['name' => 'my list']);
        //no preparation
        //action/perform
      $response = $this->getJson(route('todolist.store'));
       //we are requesting the route and receive the results as JSON
       //assertion/predict
       // Assert that the request was successful
    $response->assertStatus(200);
    //dd($response->json());
       $this->assertEquals(1, count($response->json()));
        //assert means getting or fetching the received data.


    }
}
