<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;

class Lesson extends Model
{
    protected $fillable = ['module_id', 'title', 'content_type', 'duration', 'content_url', 'description', 'path','ord'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
