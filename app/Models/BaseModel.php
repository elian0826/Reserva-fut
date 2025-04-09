<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = ['id'];

    public function getTable()
    {
        return $this->table ?? strtolower(class_basename($this));
    }
}
