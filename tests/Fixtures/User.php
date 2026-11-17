<?php

namespace ITMOSOFT\MarketplaceMessaging\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use ITMOSOFT\MarketplaceMessaging\Concerns\HasConversations;

class User extends Model
{
    use HasConversations;

    protected $table = 'test_users';
    protected $guarded = [];
    public $timestamps = false;
}
