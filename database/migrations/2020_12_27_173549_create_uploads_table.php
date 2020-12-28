<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("upload_types", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->timestamps();
        });

        Schema::create("upload_categories", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->string("subpath");
            $table->string("thumbnail")->nullable();
            $table->string("description");
            $table->foreignId("type_id")
                ->constrained("upload_types")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->timestamps();
        });

        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string("file");
            $table->string("thumbnail")->nullable();
            $table->string("description");
            $table->foreignId("category_id")
                ->constrained("upload_categories")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('upload_categories');
        Schema::dropIfExists('upload_types');
    }
}
