<?php
namespace Tests\Feature;

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementTest extends TestCase {
    use DatabaseMigrations, WithFaker;

    public function test_active()
    {
        $a = Announcement::factory()->createOne();

        $response = $this->getJson("/api/v1/announcement");

        $response->assertStatus(200)->assertJsonFragment([
            "message" => $a->message
        ]);
    }



    public function test_no_active()
    {
        Announcement::factory()->createOne(["expire_at" => Carbon::now()->subDay()]);

        $response = $this->getJson("/api/v1/announcement");

        $response->assertStatus(200)->assertJson([]);
    }


    public function test_store()
    {
        $a = Announcement::factory()->makeOne();
        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/announcement", [
            "message" => $a->message,
            "expire_at" => $a->expire_at
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(201)->assertJson([
            "message" => $a->message,
        ]);
    }


    public function test_old_date_store()
    {
        $a = Announcement::factory()->makeOne();
        $token = $this->loginUser(true);

        $response = $this->postJson("/api/v1/announcement", [
            "message" => $a->message,
            "expire_at" => Carbon::now()->subDay()
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(422);
    }
}
