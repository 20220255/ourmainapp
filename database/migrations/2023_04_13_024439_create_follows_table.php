<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            // user that is doing the following
            // user_id: "user" table, "id" field, contrained is for validation on the user table
            // that shoud have the id value.
            $table->foreignId('user_id')-> constrained();
            // This is just creating the column/field for this table.
            $table->unsignedBigInteger('followeduser');
            // this newly created column will then reference the id on the users table
            $table->foreign('followeduser')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
