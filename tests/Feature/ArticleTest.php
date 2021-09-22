<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Upload;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{

    use DatabaseMigrations, WithFaker;

    function test_articles_list()
    {
        $category = ArticleCategory::factory()->create();
        Article::factory(10)->create(["published" => true, "article_category_id" => $category->id]);

        $response = $this->getJson("/api/v1/articles/$category->slug?s=5&p=2");

        $response->assertStatus(200)->assertJsonFragment([
            "page" => 2,
            "page_size" => 5,
            "total" => 10,
            "total_pages" => 2
        ]);
    }

    function test_articles_list_unpublished()
    {
        $category = ArticleCategory::factory()->create();
        Article::factory(5)->create(["published" => false, "article_category_id" => $category->id]);

        $response = $this->getJson("/api/v1/articles/$category->slug?s=5");

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
        $response = $this->getJson("/api/v1/articles/". $article->category->slug . "/" . $article->slug);

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
        $category = ArticleCategory::factory()->createOne();
        $article = Article::factory()->makeOne();

        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/articles/".$category->slug, [
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

    function test_article_create_with_uploads()
    {
        $category = ArticleCategory::factory()->createOne();
        $uploads = Upload::factory(3)->create()->pluck("id");
        $article = Article::factory()->makeOne();

        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/articles/".$category->slug, [
            "title" => $article->title,
            "body" => $article->body,
            "short" => $article->short,
            "published" => true,
            "image" => $article->image,
            "uploads" => $uploads
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
        $this->assertEquals(count($a->uploads), 3);
    }

    function test_article_update()
    {
        $article = Article::factory()->createOne();

        $token = $this->loginUser(true);
        $newTitle = $this->faker->title;

        $response = $this->putJson("/api/v1/articles/".$article->category->slug."/".$article->id, [
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

    function test_article_update_with_uploads()
    {
        $article = Article::factory()->createOne();
        $uploads = Upload::factory(3)->create()->pluck("id");

        $token = $this->loginUser(true);
        $newTitle = $this->faker->title;

        $response = $this->putJson("/api/v1/articles/".$article->category->slug."/".$article->id, [
            "title" => $newTitle,
            "body" => $article->body,
            "short" => $article->short,
            "image" => $article->image,
            "published" => $article->published,
            "uploads" => $uploads
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "title" => $newTitle,
            "success" => true,
        ]);

        $a = Article::find($article->id);
        $this->assertEquals(count($a->uploads), 3);
    }


    function test_article_update_with_wrong_category()
    {
        $article = Article::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->putJson("/api/v1/articles/test/" . $article->id, [
            "title" => "test",
            "body" => $article->body,
            "short" => $article->short,
            "image" => $article->image,
            "published" => $article->published
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }

    function test_article_delete()
    {
        $article = Article::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->deleteJson("/api/v1/articles/". $article->category->slug ."/" . $article->id, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $a = Article::find($article->id);
        $this->assertNull($a);
    }

    function test_article_delete_with_wrong_category()
    {
        $article = Article::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->deleteJson("/api/v1/articles/test/" . $article->id, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }
}
