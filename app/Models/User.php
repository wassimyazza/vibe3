<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'city',
        'birthday',
        'cover_photo',
        'pseudo',
        'bio',
        'isActive',
        'password',
        'last_seen'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
        'last_seen' => 'datetime'
    ];

    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->diffInMinutes(now()) < 5;
    }

    public function sentRequests(): HasMany
    {
        return $this->hasMany(Ami::class, 'id_sender');
    }

    public function receivedRequests(): HasMany
    {
        return $this->hasMany(Ami::class, 'id_receiver');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commantaire::class);
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'ami', 'id_sender', 'id_receiver')
            ->wherePivot('status', 'accepted')
            ->select('users.*', 'ami.id_sender as pivot_id_sender', 'ami.id_receiver as pivot_id_receiver')
            ->union(
                $this->belongsToMany(User::class, 'ami', 'id_receiver', 'id_sender')
                    ->wherePivot('status', 'accepted')
                    ->select('users.*', 'ami.id_sender as pivot_id_sender', 'ami.id_receiver as pivot_id_receiver')
            );
    }

    public function getFriends()
    {
        $sentFriends = $this->belongsToMany(User::class, 'ami', 'id_sender', 'id_receiver')
            ->wherePivot('status', 'accepted')
            ->select('users.*', 'ami.id_sender as pivot_id_sender', 'ami.id_receiver as pivot_id_receiver')
            ->get();

        $receivedFriends = $this->belongsToMany(User::class, 'ami', 'id_receiver', 'id_sender')
            ->wherePivot('status', 'accepted')
            ->select('users.*', 'ami.id_sender as pivot_id_sender', 'ami.id_receiver as pivot_id_receiver')
            ->get();

        return $sentFriends->merge($receivedFriends);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function conversations() {
        return $this->hasMany(Conversation::class, 'user_one_id')
            ->orWhere('user_two_id', $this->id);
    }
}