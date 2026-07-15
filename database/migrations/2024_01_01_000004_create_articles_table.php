<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reference')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('min_quantity')->default(1);
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock'])->default('out_of_stock');
            $table->enum('article_status', ['Nouveau', 'Ancien'])->default('Nouveau');

            // Extra fields from presentation
            $table->string('unit')->nullable();          // pièce, kg, m3…
            $table->string('brand')->nullable();          // marque
            $table->string('nature')->nullable();         // Conditionelle, Systématique, Critique
            $table->string('supplier')->nullable();       // fournisseur
            $table->string('ocp_code')->nullable();       // Code Article OCP
            $table->text('description')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
