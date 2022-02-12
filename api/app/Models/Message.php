<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id_from',
        'user_id_to',
        'text',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user_from()
    {
        return $this->hasOne(User::class, 'id', 'user_id_from');
    }

    public function user_to()
    {
        return $this->hasOne(User::class, 'id', 'user_id_to');
    }

    public function last_sent_messages()
    {
        return $this->hasOne(Message::class, 'id', 'user_id_from')->latestOfMany();
    }

    public function last_received_messages()
    {
        return $this->hasOne(Message::class, 'id', 'user_id_to')->latestOfMany();
    }
}
