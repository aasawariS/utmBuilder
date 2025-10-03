<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtmLink extends Model
{
protected $fillable = [
'author','title','slug','resource_type','campaign',
'original_url','utm_url','context_text'
];
}

