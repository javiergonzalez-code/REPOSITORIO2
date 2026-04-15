<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $row) {
            // Cambiamos el tipo de causer_id a string para que acepte 'SUPERADMIN01'
            $row->string('causer_id', 15)->nullable()->change();
            // También es recomendable cambiar subject_id por si auditas modelos con llaves string
            $row->string('subject_id', 15)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $row) {
            $row->bigInteger('causer_id')->unsigned()->nullable()->change();
            $row->bigInteger('subject_id')->unsigned()->nullable()->change();
        });
    }
};