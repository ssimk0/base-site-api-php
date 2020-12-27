<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PageCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    function test_page_categories()
    {

        PageCategory::factory(3)->create();
        $response = $this->getJson("/api/v1/pages/");

        $response->assertStatus(200)->assertJsonCount(3);
    }

    function test_page_categories_empty()
    {

        $response = $this->getJson("/api/v1/pages/");

        $response->assertStatus(200)->assertJsonCount(0);
    }

    function test_page_category_get_pages()
    {
        $category = PageCategory::factory()->create();
        Page::factory(3)->create(["page_category_id" => $category->id]);
        Page::factory(3)->create(); // in other categories

        $response = $this->getJson("/api/v1/pages/".$category->slug);

        $response->assertStatus(200)->assertJsonCount(3);
    }

    function test_page_category_get_pages_empty()
    {

        $response = $this->getJson("/api/v1/pages/test");

        $response->assertStatus(404);
    }


    function test_get_page_by_slug()
    {
        $category = PageCategory::factory()->hasPages(1)->createOne();
        $page = $category->pages[0];

        $response = $this->getJson("/api/v1/pages/".$category->slug.'/'.$page->slug);

        $response->assertStatus(200)->assertJsonFragment([
            "title" => $page->title,
            "body" => $page->body,
            "slug" => $page->slug
        ]);
    }

    function test_page_category_get_pages_different_category()
    {
        $category = PageCategory::factory()->create();
        $page = Page::factory()->createOne(); // in other categories

        $response = $this->getJson("/api/v1/pages/".$category->slug.'/'.$page->slug);

        $response->assertStatus(404);
    }


    function test_page_category_get_pages_not_exists()
    {
        $response = $this->getJson("/api/v1/pages/category/test");

        $response->assertStatus(404);
    }

    function test_page_create()
    {
        $pageCategory = PageCategory::factory()->createOne();
        $page = Page::factory()->makeOne();

        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/pages/".$pageCategory->slug, [
            "title" => $page->title,
            "body" => $page->body
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            "title" => $page->title,
            "body" => $page->body,
            "success" => true,
        ]);

       $id = $response->json("id");
       // check is is persisted to DB
       $p = Page::find($id);

       $this->assertEquals($p->title, $page->title);
    }

    function test_page_update_by_id()
    {
        $page = Page::factory()->createOne();

        $token = $this->loginUser(true);
        $newTitle = $this->faker->title;

        $response = $this->putJson("/api/v1/pages/". $page->id , [
            "title" => $newTitle,
            "body" => $page->body
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        // check is is persisted to DB
        $p = Page::find($page->id);

        $this->assertEquals($p->title, $newTitle);
    }

    function test_page_update_by_slug()
    {
        $page = Page::factory()->createOne();

        $token = $this->loginUser(true);
        $newTitle = $this->faker->title;

        $response = $this->putJson("/api/v1/pages/". $page->page_category->slug .'/'. $page->slug , [
            "title" => $newTitle,
            "body" => $page->body
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        // check is is persisted to DB
        $p = Page::find($page->id);

        $this->assertEquals($p->title, $newTitle);
    }

    function test_page_delete_by_slug()
    {
        $page = Page::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->deleteJson("/api/v1/pages/" . $page->page_category->slug . '/' . $page->slug, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        // check is is persisted to DB
        $p = Page::find($page->id);

        $this->assertNull($p);
    }


        function test_page_delete_by_id()
    {
        $page = Page::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->deleteJson("/api/v1/pages/". $page->id , [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        // check is is persisted to DB
        $p = Page::find($page->id);

        $this->assertNull($p);
    }
}
