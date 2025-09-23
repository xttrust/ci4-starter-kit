<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationReadModel extends Model
{
    protected $table            = 'notification_reads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','last_read_at'];
}
