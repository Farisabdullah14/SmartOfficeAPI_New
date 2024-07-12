<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KunciRuangan extends Model
{
    protected $table = 'kunci_ruangan';

    protected $fillable = [
        'id_key',
        'id_ruangan',
        'url_ruangan',
        'status',
        'id_user',
    ];}
