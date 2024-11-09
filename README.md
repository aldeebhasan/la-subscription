<div style="text-align: center;">
<h1 >LA SUBSCRIPTION </h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/aldeebhasan/la-subscription.svg?style=flat-square)](https://packagist.org/packages/aldeebhasan/la-subscription)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/aldeebhasan/la-subscription/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/aldeebhasan/la-subscription/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/aldeebhasan/la-subscription/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/aldeebhasan/la-subscription/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/aldeebhasan/la-subscription.svg?style=flat-square)](https://packagist.org/packages/aldeebhasan/la-subscription)

</div>

## About

LA SUBSCRIPTION is a robust package designed to simplify the implementation and management of subscription-based services within Laravel applications. It provides a flexible approach to handle various
subscription plans, making it ideal for SaaS applications, membership sites, and any system requiring recurring billing.

## Installation

You can install the package via composer:

```bash
composer require aldeebhasan/la-subscription
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="la-subscription-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="la-subscription-config"
```

This is the contents of the published config file:

```php
return [
    'prefix' => env("LA_SUBSCRIPTION_PREFIX", "la"),
    'grace_period' => env("LA_GRACE_PERIOD", 7),
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="la-subscription-views"
```

## Usage

To start using this package you need first to add `HasSubscription` Trait to the model you want to handle the subscription for it.

```php
namespace App\Models;

use Aldeebhasan\LaSubscription\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasSubscriptions;
}
```

## Setup Features

The basic unit of any subscription is the features, you can group them under specific groups according to there type.

You can create features as follow:

```php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Aldeebhasan\LaSubscription\Models\Feature;
use  Aldeebhasan\LaSubscription\Models\Group;
use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;

class FeatureSeeder extends Seeder
{
    public function run()
    {
        $group = Group::create([
        'name' => "Sales",
        'type' => GroupTypeEnum::FEATURE
        ]);

        $feature_1 = Feature::create([
            'limited'     => false,
            'name'        => 'Invoice Managements',
            'code'        => 'invoices',
            'group_id'    => $group->id,
        ]);

        $feature_2 = Feature::create([
            'limited'     => true,
            'name'        => 'User Managements',
            'code'        => 'users',
            'group_id'    => $group->id,
        ]);
    }
}
```

The features can be limited/unlimited. The limited feature has specific quota need to be specified, and the subnscriber can't use more that the specified quota.
On the other hand, the unlimited feature has unlimited number of usage quota. This can be identified using the `limited` param.

## Setup Plans

Plans work like a group of features that will be available to the subscriber.
You can define a new plan as follow:

```php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Models\Group;
use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $group = Group::create([
        'name' => "Premium Plans",
        'type' => GroupTypeEnum::PLAN
        ]);

        $plan = Product::create([
            'name'         => "Starter",
            'description'  => 'Description of starter plan',
            'code'         => 'starter',
            'group_id'     => $group->id,
            'price'        => 15;
            'price_yearly' => 12;
        ]);
        
        $feature_1 = Feature::whereCode('users')->first();
        $feature_2 = Feature::whereCode('users')->first();
        $plan->features()->attach($feature_1, ['value' => 20]);
        $plan->features()->attach($feature_2);
    }
}
```

## Handle Subscriptions

Now everything is ready to create a new subscription for the subscribing model :

### New Subscription

```php

$subscriber = User::create(['name'=>'Subscriber']);
$plan = \Aldeebhasan\LaSubscription\Models\Product::firstWhere('code','starter');

\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->subscribeTo($plan)
//OR
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->subscribeTo($plan, now()->endOfMonth())
//OR
$period = 12; // in months
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->subscribeTo($plan, now()->endOfMonth(), $period)

```

### Switch  Plan

If you already subscribed to a plan you want to change the subscription plan to another one (like upgrade the subscription), you can use `switchTo`.
Note that you can switch the plan at a specific time in the future and with different billing cycle.

```php

$newPlan = \Aldeebhasan\LaSubscription\Models\Product::firstWhere('code','silver');

\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->switchTo($newPlan)
//OR
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->switchTo($newPlan, now()->endOfMonth())
//OR
$period = 6; // in months
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->switchTo($plan, now()->endOfMonth(), $period)
```

> [!NOTE]
> By switching subscription , the old subscription will be set at suppressed status, and the suppressed date will be stored.

> [!NOTE]
> The `switchTo` function has an optional parameter named as `$withPlugins`. This param indicate if you want to install the current installed plugins with the switched plan or not.
> "we will talk about the Plugins later"

### Renew Subscription

In case you want to renew the current subscription you can do it as follow:

```php
$period = 6; // in months
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->renew($period)
```

> [!NOTE]
> The `renew` function has an optional parameter named as `$withPlugins`. This param indicate if you want to renew the plugins with the plan or not.
> "we will talk about the Plugins later"

### Unlimited Subscription

At any point of time, if you want to bypass all the subscription features check for specific subscriber, you can enable `Unlimited access` for him.
The will enable him to access to all the features with out any limitations.

```php
$period = 6; // in months
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->setUnlimitedAccess(true)
// OR
$subscriber->setUnlimitedAccess(true)
```

### Cancel/Resume Subscription

At any point of time, if you want to cancel the user subscription or resumed canceled one, you can do it as follow:

```php
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->cancel()
// OR
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->cancel(now()->endOfMonth())

//To resume
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->resume()
```

## Setup Plugins

Plugins in this package represent a set of figures that can be added to the subscription along with the plan.
As example, the user at the starter plan may want to enable a plugin that give him access to specific features that are not enabled in his plan.
Plugins can be considered as a small plans with limited set of features.

Plugins can be RECURRING/NON_RECURRING,  
- The RECURRING  plugin  will follow the same subscription billing cycle, if not renewed, the subscriber will loss the access to its features.
- The NON_RECURRING plugins will be enabled forever, there is no need to renew it at all.

```php
$group = Group::create([
'name' => "Accounting Plugins",
'type' => GroupTypeEnum::PLUGIN
]);

$plugin = Product::create([
    'name'         => "Advanced reports",
    'description'  => 'Description of Advanced reports',
    'code'         => 'advanced-reports',
    'group_id'     => $group->id,
    'type'         => BillingCycleEnum::RECURRING, // Or Un-Recurring
    'price'        => 15;
    'price_yearly' => 12;
]);


\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->addPlugin($plugin);
//OR as specific time
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->addPlugin($plugin, now()->endOfMonth());
//OR you can specify the model that caused this addition (ex: the Invoice item or a manager user), by default it will be the subscriber himself.
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->addPlugin($plugin, now()->endOfMonth(), $manager);
```

At any point of time you can cancel any plugin or resume it as follow:

```php
$causative = $subscriber->manager; // optional
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->cancelPlugin($plugin , $causative)
//To resume
\Aldeebhasan\LaSubscription\LaSubscription::make($subscriber)->resumePlugin($plugin , $causative)
```

> [!WARNING]
> When a plugin is cancelled, it will not be renewed with the plan any more.

## Features availability

You can check if the subscriber can consume feature/features as follow:

```php
$code = "feature-code" // or  ["feature-1-code" ,"feature-2-code" ]
$subscriber->canConsume($code );
```

You can check if the subscriber can consume any feature/features as follow:

```php
$code = ["feature-1-code" ,"feature-2-code" ]
$subscriber->canConsumeAny($code);
```

For the features that has limited quotas, you can register the consumption of that feature as follow

```php
$code = 'feature-code';
$amount = 1; // 1 is the default value
$subscriber->consume($code, $amount);
```

For any reason that require you to return the consumption value to the subscriber you can do that with the follow:

```php
$code = 'feature-code';
$amount = 1; // 1 is the default value
$subscriber->retrieve($code, $amount);
```

To retrieve the current consumption of specific feature

```php
$subscriber->getCurrentConsumption('feature-code');
```

To retrieve the remaining balance of limited features

```php
$subscriber->getBalance('feature-code');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hasan Deeb](https://github.com/aldeebhasan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
