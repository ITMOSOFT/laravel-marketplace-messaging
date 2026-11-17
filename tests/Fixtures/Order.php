<?php

namespace ITMOSOFT\MarketplaceMessaging\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'test_orders';
    protected $guarded = [];
    public $timestamps = false;
}
