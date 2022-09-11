<?php

namespace Modules\Dashboard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

class DashboardOption extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'values',
    'values->access_token',
    'values->campaign_id',
  ];
  protected $casts = [
    'values' => 'array',
  ];

  protected static function newFactory()
  {
    // return \Modules\Dashboard\Database\factories\DashboardOptionFactory::new();
  }
}
