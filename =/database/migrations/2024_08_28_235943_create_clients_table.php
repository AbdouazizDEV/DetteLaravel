<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('surnom')->unique();
            $table->string('telephone_portable')->unique()->regex('/^(\+?[0-9]{1,4})?([0-9]{10})$/'); // Validation avec regex
            $table->unsignedBigInteger('user_id')->nullable(); // Clé étrangère vers la table users
            $table->timestamps();

            // Définition de la clé étrangère et de la contrainte unique
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id'); // Assurer que chaque utilisateur ne peut avoir qu'un client
            $table->string('qrcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
   
    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::table('client', function (Blueprint $table) {
            $table->dropColumn('qrcode');
        });
    }
};
