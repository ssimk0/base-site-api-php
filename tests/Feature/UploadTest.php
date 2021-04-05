<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Upload;
use App\Models\UploadCategory;
use App\Models\UploadType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadTest extends TestCase
{

    use DatabaseMigrations, WithFaker;

    function test_upload_categories_list()
    {
        $type = UploadType::factory()->createOne();
        UploadCategory::factory(5)->create(["type_id" => $type->id]);

        $response = $this->getJson("/api/v1/uploads/" . $type->slug);

        $response->assertStatus(200)->assertJsonCount(5);
    }


    function test_uploads_by_category_list()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        Upload::factory(10)->create(["category_id" => $category->id]);

        $response = $this->getJson("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "?s=5&p=2");

        $response->assertStatus(200)->assertJsonFragment([
            "total" => 10,
            "page_size" => 5,
            "page" => 2
        ]);
    }


    function test_upload_detail()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $upload = Upload::factory()->createOne(["category_id" => $category->id]);

        $response = $this->getJson("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "/" . $upload->id);

        $response->assertStatus(200)->assertJsonFragment([
            "file" => $upload->file
        ]);
    }


    function test_upload_update()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $upload = Upload::factory()->createOne(["category_id" => $category->id]);
        $token = $this->loginUser(true);

        $newDesc = $this->faker->sentence;
        $response = $this->putJson("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "/" . $upload->id, [
            "description" => $newDesc,
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true
        ]);

        $u = Upload::find($upload->id);

        $this->assertEquals($newDesc, $u->description);
    }


    function test_upload_latest()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        Upload::factory()->createOne(["category_id" => $category->id]);
        $token = $this->loginUser(true);
        Http::fake([
            '*' => Http::response('Hello World', 200, ['Headers']),
        ]);

        $response = $this->get("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "/latest", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    function test_upload_download()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $upload = Upload::factory()->createOne(["category_id" => $category->id]);
        $token = $this->loginUser(true);
        Http::fake([
            '*' => Http::response('Hello World', 200, ['Headers']),
        ]);

        $response = $this->get("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "/" . $upload->id . "/download", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }

    function test_upload_download_wrong_url()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        Upload::factory()->createOne(["category_id" => $category->id]);
        $token = $this->loginUser(true);
        Http::fake([
            '*' => Http::response('Hello World', 500, ['Headers']),
        ]);

        $response = $this->get("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "/latest", [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(404);
    }


    function test_upload_delete()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $upload = Upload::factory()->createOne(["category_id" => $category->id]);
        $token = $this->loginUser(true);

        $newDesc = $this->faker->sentence;
        $response = $this->deleteJson("/api/v1/uploads/" . $type->slug . "/" . $category->slug . "/" . $upload->id, [
            "description" => $newDesc,
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true
        ]);

        $u = Upload::find($upload->id);

        $this->assertNull($u);
    }


    function test_upload_category_create()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->makeOne(["type_id" => $type->id]);
        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/uploads/" . $type->slug . "/", [
            "description" => $category->description,
            "name" => $category->name,
            "subPath" => $category->sub_path,
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            "success" => true,
            "sub_path" => $category->sub_path
        ]);
    }

    function test_upload_category_update()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $token = $this->loginUser(true);

        $newDesc = $this->faker->sentence;
        $response = $this->putJson("/api/v1/uploads/" . $type->slug . "/" . $category->id, [
            "description" => $newDesc
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $c = UploadCategory::find($category->id);
        $this->assertEquals($newDesc, $c->description);
    }


    function test_upload_category_delete()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);

        $token = $this->loginUser(true);
        $response = $this->deleteJson("/api/v1/uploads/" . $type->slug . "/" . $category->id, [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $c = UploadCategory::find($category->id);
        $this->assertNull($c);
    }

    function test_upload_image()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $token = $this->loginUser(true);
        $response = $this->postJson("/api/v1/uploads/" . $type->slug . "/" . $category->slug, [
            "file" => $file,
            "description" => $this->faker->sentence
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            "success",
            "file",
            "thumbnail",
            "created_at",
            "updated_at",
            "id",
            "description"
        ]);
    }

    function test_upload_file()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id, "thumbnail" => null]);
        Storage::fake('avatars');

        $file = UploadedFile::fake()->create('avatar.pdf');

        $token = $this->loginUser(true);
        $response = $this->postJson("/api/v1/uploads/" . $type->slug . "/" . $category->slug, [
            "file" => $file,
            "description" => $this->faker->sentence
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonStructure([
            "success",
            "file",
            "thumbnail",
            "created_at",
            "updated_at",
            "id",
            "description"
        ]);

        $c = UploadCategory::find($category->id);

        $this->assertEquals($c->thumbnail, $response->json("file"));
    }
}
