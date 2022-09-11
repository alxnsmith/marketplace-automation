<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('dashboard_options', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100)->index();
      $table->foreignIdFor(User::class)->unique();
      $table->json('values')->default(new Expression('(JSON_ARRAY())'));;
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
    Schema::dropIfExists('dashboard_options');
  }
};
