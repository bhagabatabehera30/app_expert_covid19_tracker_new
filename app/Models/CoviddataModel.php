<?php namespace App\Models;

use CodeIgniter\Model;

class CoviddataModel extends Model
{
   protected $table      = 'tbl_covid19_data';
   protected $primaryKey = 'id';

   protected $returnType = 'array';
   protected $useSoftDeletes = false;

   protected $allowedFields = [
     'country',
     'last_updated',
     'new_infections',
     'new_deaths',
     'new_recovered'
   ];

   protected $useTimestamps = true;
   protected $createdField  = 'created_at';
   protected $updatedField  = 'updated_at';
   protected $deleted_at    = 'deleted_at';
}