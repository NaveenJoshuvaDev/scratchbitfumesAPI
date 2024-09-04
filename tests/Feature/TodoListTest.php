<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        //$this->withoutExceptionHandling();
        //preparation/prepare
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
