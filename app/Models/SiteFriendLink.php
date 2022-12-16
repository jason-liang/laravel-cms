<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SiteModel;
use Encore\Admin\Traits\ModelTree;

class SiteFriendLink extends SiteModel
{
    use HasFactory, ModelTree {
        ModelTree::boot as modelTreeBoot;
    }

    protected $table = "site_friend_links";

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        $this->setTitleColumn('name');
    }
}
