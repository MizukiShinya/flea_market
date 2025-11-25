<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete(); 
            $table->string('item_name');
            $table->string('item_image_url')->nullable();
            $table->string('condition');
            $table->string('brand')->nullable(); 
            $table->integer('price');
            $table->text('detail')->nullable();
            $table->integer('like_count')->default(0);
            $table->boolean('is_sold')->default(false);
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropColumn('profile_id');
        });;
    }
}
