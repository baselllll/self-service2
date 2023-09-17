<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunUser extends Model
{
    protected $connection = "oracle";
    protected $table = 'FND_USER';
}
