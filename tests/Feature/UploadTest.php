<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Upload;
use App\Models\UploadCategory;
use App\Models\UploadType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UploadTest extends TestCase
{

    use DatabaseMigrations, WithFaker;

    function test_upload_categories_list()
    {
        $type = UploadType::factory()->createOne();
        UploadCategory::factory(5)->create(["type_id" => $type->id]);

        $response = $this->getJson("/api/v1/uploads/".$type->slug);

        $response->assertStatus(200)->assertJsonCount(5);
    }


    function test_uploads_by_category_list()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        Upload::factory(10)->create(["category_id" => $category->id]);

        $response = $this->getJson("/api/v1/uploads/".$type->slug."/".$category->slug."?s=5&p=2");

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

        $response = $this->getJson("/api/v1/uploads/".$type->slug."/".$category->slug."/".$upload->id);

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
        $response = $this->putJson("/api/v1/uploads/".$type->slug."/".$category->slug."/".$upload->id, [
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



    function test_upload_delete()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $upload = Upload::factory()->createOne(["category_id" => $category->id]);
        $token = $this->loginUser(true);

        $newDesc = $this->faker->sentence;
        $response = $this->deleteJson("/api/v1/uploads/".$type->slug."/".$category->slug."/".$upload->id, [
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

        $response = $this->postJson("/api/v1/uploads/".$type->slug."/", [
            "description" => $category->description,
            "name" => $category->name,
            "subpath" => $category->subpath,
        ], [
             'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJsonFragment([
            "success" => true,
            "subpath" => $category->subpath
        ]);
    }

    function test_upload_category_update()
    {
        $type = UploadType::factory()->createOne();
        $category = UploadCategory::factory()->createOne(["type_id" => $type->id]);
        $token = $this->loginUser(true);

        $newDesc = $this->faker->sentence;
        $response = $this->putJson("/api/v1/uploads/".$type->slug."/".$category->id, [
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
        $response = $this->deleteJson("/api/v1/uploads/".$type->slug."/".$category->id, [], [
             'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            "success" => true,
        ]);

        $c = UploadCategory::find($category->id);
        $this->assertNull($c);
    }

}
