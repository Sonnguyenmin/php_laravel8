<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    public $timestamp = true;
    protected $table = 'tbl_brand';
    protected $fillable = [
        'brand_name','brand_desc', 'brand_status', 'brand_order'
    ];
}
?>
