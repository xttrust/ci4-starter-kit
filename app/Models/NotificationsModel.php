<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationsModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type','title','body','user_id','ip_address','is_read','created_at','updated_at','activity_id'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
}
