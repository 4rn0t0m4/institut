<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id','title','slug','excerpt','content','status',
        'featured_image','meta_title','meta_description','categories','tags','published_at',
    ];
    protected $casts = ['categories'=>'array','tags'=>'array','published_at'=>'datetime'];

    public function author() { return $this->belongsTo(User::class,'user_id'); }

    public function scopePublished($query)
    {
        return $query->where('status','published')->where('published_at','<=',now());
    }
}
