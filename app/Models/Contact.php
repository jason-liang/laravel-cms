<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SiteModel;

class Contact extends SiteModel
{
    use HasFactory;

    protected $table = "contacts";
}
