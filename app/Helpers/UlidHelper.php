<?php


namespace App\Helpers;


use Illuminate\Support\Str;

trait UlidHelper
{
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::ulid()->toString();
        });
    }
    
    public function getIncrementing()
    {
        return false;
    }
    
    public function getKeyName()
    {
        return 'id';
    }
    
    public function getKeyType()
    {
        return 'string';
    }
}
