<?php

namespace App\Models;

use App\Helpers\UlidHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory, UlidHelper;

    protected $guarded = [ 'id' ];

    protected $with = [
        'user:id,name,email,phone',
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
