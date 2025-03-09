<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add the required columns
            $table->unsignedBigInteger('order_id')->after('id');
            $table->unsignedBigInteger('product_id')->after('order_id');
            $table->integer('quantity')->after('product_id');
            $table->decimal('subtotal', 10, 2)->after('quantity');

            // Add foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);

            // Drop the columns
            $table->dropColumn('order_id');
            $table->dropColumn('product_id');
            $table->dropColumn('quantity');
            $table->dropColumn('subtotal');
        });
    }
}