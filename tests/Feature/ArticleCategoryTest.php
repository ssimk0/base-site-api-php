<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleCategoryTest extends TestCase
{

    use DatabaseMigrations, WithFaker;

    function test_article_category_list()
    {
        ArticleCategory::factory(5)->create();

        $response = $this->get("/api/v1/articles");

        $response->assertStatus(200);
        $response->assertJsonCount(6); // one category is created in the migrations it self as fallback
    }

    function test_article_category_list_with_lang()
    {
        ArticleCategory::factory(5)->create();
        ArticleCategory::factory(5)->create(["lang" => "sk"]);

        $response = $this->get("/api/v1/articles?lang=sk");

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    function test_article_category_create()
    {
        $articleCategory = ArticleCategory::factory()->makeOne();

        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/articles/", [
            "name" => $articleCategory->name,
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            "name" => $articleCategory->name,
        ]);

        $id = $response->json("id");

        $a = ArticleCategory::find($id);
        $this->assertEquals($a->name, $articleCategory->name);
    }

    function test_article_category_create_with_lang()
    {
        $articleCategory = ArticleCategory::factory()->makeOne();

        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/articles/", [
            "name" => $articleCategory->name,
            "lang" => "en"
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            "name" => $articleCategory->name,
            "lang" => "en"
        ]);

        $id = $response->json("id");

        $a = ArticleCategory::find($id);
        $this->assertEquals($a->name, $articleCategory->name);
        $this->assertEquals($a->lang, "en");
    }

    function test_article_category_update()
    {
        $articleCategory = ArticleCategory::factory()->createOne();

        $token = $this->loginUser(true);
        $newName = $this->faker->sentence;

        $response = $this->putJson("/api/v1/articles/".$articleCategory->slug, [
            "name" => $newName,
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $a = ArticleCategory::find($articleCategory->id);
        $this->assertEquals($a->name, $newName);

    }

    function test_article_delete()
    {
        $articleCategory = ArticleCategory::factory()->createOne();

        $token = $this->loginUser(true);

        $response = $this->deleteJson("/api/v1/articles/". $articleCategory->id, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $a = Article::find($articleCategory->id);
        $this->assertNull($a);
    }
}
