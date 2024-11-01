<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $prefix = config('subscription.prefix');
        Schema::create("{$prefix}_groups", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->enum("type", ['plan', 'product', 'feature']);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create("{$prefix}_products", function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->text("description");
            $table->foreign("group_id")->references('id')->on("{$prefix}_groups");
            $table->boolean('active')->default(true);
            $table->enum("type", ['recurring', 'non-recurring']);
            $table->double("price")->default(0);
            $table->double("price_yearly")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_features", function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->text("description");
            $table->foreign("group_id")->references('id')->on("{$prefix}_groups");
            $table->boolean('active')->default(true);
            $table->boolean('limited')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_product_feature", function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreign("product_id")->references('id')->on("{$prefix}_products");
            $table->foreign("feature_id")->references('id')->on("{$prefix}_features");
            $table->boolean('active')->default(true);
            $table->double('value')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_subscriptions", function (Blueprint $table) {
            $table->id();
            $table->morphs("owner");
            $table->timestamp("start_at")->nullable();
            $table->timestamp("end_at")->nullable();
            $table->timestamp("trial_end_at")->nullable();
            $table->timestamp("billing_period")->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create("{$prefix}_subscription_items", function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreign("subscription_id")->references('id')->on("{$prefix}_subscriptions");
            $table->string("code");
            $table->morphs("product");
            $table->integer("number")->default(1);
            $table->timestamp("start_at");
            $table->timestamp("end_at")->nullable();
            $table->enum("type", ['recurring', 'non-recurring']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_subscription_item_transactions", function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreign("subscription_item_id")->references('id')->on("{$prefix}_subscription_items");
            $table->timestamp("start_at");
            $table->timestamp("end_at")->nullable();
            $table->morphs("causative");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {

    }
};
