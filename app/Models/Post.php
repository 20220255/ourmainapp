<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;
    use HasFactory;

    // this automatically fill the request array in the contoller. No need to specify every fields
    // in the fields.
    protected $fillable = ['title', 'body', 'user_id'];

    public function toSearchableArray() {
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }

    public function user() {
        // this will access the table or the model of the table (User model) that the post table
        // has a relation to (users table).
        return $this->belongsTo(User::class, 'user_id');
    }
}
