<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{

    use DatabaseMigrations, WithFaker;

    function test_articles_list()
    {
        Article::factory(10)->create(["published" => true]);

        $response = $this->getJson("/api/v1/articles?s=5&p=2");

        $response->assertStatus(200)->assertJsonFragment([
            "page" => 2,
            "page_size" => 5,
            "total" => 10,
            "total_pages" => 2
        ]);
    }

    function test_articles_list_unpublished()
    {
        Article::factory(5)->create(["published" => false]);

        $response = $this->getJson("/api/v1/articles?s=5");

        $response->assertStatus(200)->assertJsonFragment([
            "page" => 1,
            "page_size" => 5,
            "total" => 0,
            "total_pages" => 1
        ]);

    }

    function test_article_detail()
    {
        $article = Article::factory()->createOne(["published" => true]);

        $response = $this->getJson("/api/v1/articles/" . $article->slug);

        $response->assertStatus(200)->assertJsonFragment([
            "id" => $article->id,
            "title" => $article->title,
            "slug" => $article->slug,
            "body" => $article->body,
        ]);

        $a = Article::find($article->id);
        $this->assertEquals($a->viewed, $article->viewed+1);
    }


    function test_article_detail_unpublished()
    {
        $article = Article::factory()->createOne(["published" => false]);

        $response = $this->getJson("/api/v1/articles/" . $article->slug);

        $response->assertStatus(404);
    }

    function test_article_create()
    {
        $article = Article::factory()->makeOne();

        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/articles", [
            "title" => $article->title,
            "body" => $article->body,
            "short" => $article->short,
            "published" => true,
            "image" => $article->image
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            "title" => $article->title,
            "body" => $article->body,
            "short" => $article->short,
            "published" => true,
            "image" => $article->image,
            "success" => true,
        ]);

        $id = $response->json("id");

        $a = Article::find($id);
        $this->assertEquals($a->title, $article->title);
    }

    function test_article_update()
    {
        $article = Article::factory()->createOne();

        $token = $this->loginUser(true);
        $newTitle = $this->faker->title;

        $response = $this->putJson("/api/v1/articles/".$article->id, [
            "title" => $newTitle,
            "body" => $article->body,
            "short" => $article->short,
            "image" => $article->image,
            "published" => $article->published
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "title" => $newTitle,
            "success" => true,
        ]);

        $a = Article::find($article->id);
        $this->assertEquals($a->title, $newTitle);
    }

    function test_article_delete()
    {
        $article = Article::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->deleteJson("/api/v1/articles/" . $article->id, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $a = Article::find($article->id);
        $this->assertNull($a);
    }
}
