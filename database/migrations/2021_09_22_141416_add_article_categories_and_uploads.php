<?php

use App\Models\ArticleCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArticleCategoriesAndUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $c = new ArticleCategory(["name" => "news", "slug" => "news"]);
        $c->save();
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('article_category_id')
                ->default(1)
                ->constrained('article_categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('article_upload', function (Blueprint $table) {
            $table->primary(['article_id', 'upload_id'], 'article_upload_id`');

            $table->foreignId('article_id')
                ->constrained('articles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('upload_id')
                ->constrained('uploads')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
        Schema::dropIfExists('article_upload');
        Schema::dropIfExists('article_categories');
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('article_category_id');
        });
    }
}
