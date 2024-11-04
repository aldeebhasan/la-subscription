<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
        Schema::create("{$prefix}_products", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->text("description");
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Group::class);
            $table->boolean('active')->default(true);
            $table->enum("type", ['recurring', 'non-recurring']);
            $table->double("price")->default(0);
            $table->double("price_yearly")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_features", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code")->unique();
            $table->text("description");
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Group::class);
            $table->boolean('active')->default(true);
            $table->boolean('limited')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_product_feature", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Product::class);
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Feature::class);
            $table->boolean('active')->default(true);
            $table->double('value')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_subscriptions", function (Blueprint $table) {
            $table->id();
            $table->morphs("subscriber");
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Product::class, 'plan_id');
            $table->timestamp("start_at")->nullable();
            $table->timestamp("end_at")->nullable();
            $table->timestamp("supersede_at")->nullable();
            $table->timestamp("canceled_at")->nullable();
            $table->timestamp("billing_period")->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create("{$prefix}_subscription_contracts", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Subscription::class);
            $table->string("code");
            $table->morphs("product");
            $table->integer("number")->default(1);
            $table->timestamp("start_at");
            $table->timestamp("end_at")->nullable();
            $table->boolean("auto_renew")->default(true);
            $table->enum("type", ['recurring', 'non-recurring']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_subscription_contract_transactions", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\SubscriptionContract::class);
            $table->string("type");
            $table->timestamp("start_at");
            $table->timestamp("end_at")->nullable();
            $table->morphs("causative");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("{$prefix}_subscription_quotas", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Subscription::class);
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Feature::class);
            $table->string("code");
            $table->timestamp("end_at")->nullable();
            $table->boolean('limited')->default(false);
            $table->double('quota')->unsigned()->default(0);
            $table->double('consumed')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create("{$prefix}_feature_consumptions", function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Subscription::class);
            $table->foreignIdFor(Aldeebhasan\LaSubscription\Models\Feature::class);
            $table->string("code");
            $table->double('consumed')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        $prefix = config('subscription.prefix');
        Schema::dropIfExists("{$prefix}_subscription_consumptions");
        Schema::dropIfExists("{$prefix}_subscription_contract_transactions");
        Schema::dropIfExists("{$prefix}_subscription_contracts");
        Schema::dropIfExists("{$prefix}_subscriptions");
        Schema::dropIfExists("{$prefix}_product_feature");
        Schema::dropIfExists("{$prefix}_features");
        Schema::dropIfExists("{$prefix}_products");
        Schema::dropIfExists("{$prefix}_groups");
    }
};
