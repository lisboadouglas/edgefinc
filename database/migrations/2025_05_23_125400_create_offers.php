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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('cpf')->index();
            $table->unsignedBigInteger('instituicao_id')->index();
            $table->string('instituicao_nome')->nullable();
            $table->string('modalidade_cod')->nullable()->index();
            $table->string('modalidade_nome')->nullable();
            $table->decimal('valor_min', 10, 2)->nullable();
            $table->decimal('valor_max', 10, 2)->nullable();
            $table->decimal('valor_medio', 10, 2)->nullable();
            $table->decimal('taxa_juros', 5, 2)->nullable();
            $table->decimal('custo_total', 10, 2)->nullable();
            $table->integer('quantidade_parcelas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
