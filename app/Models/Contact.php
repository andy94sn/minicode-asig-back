<?php

namespace App\Models;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Guid\Guid;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $fillable = ['token', 'name', 'page', 'email', 'phone', 'message'];
}

