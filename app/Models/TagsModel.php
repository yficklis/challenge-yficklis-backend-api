<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagsModel extends Model
{
    use HasFactory;

    public $table = 'tags';

    protected $fillable = [
        'name',
        'description',
        'id_repository',
        'created_tag_by_username',
    ];
}
