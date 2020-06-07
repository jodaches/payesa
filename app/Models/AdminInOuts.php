<?php
#app/Models/AdminStore.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminInOuts extends Model
{    
    public $table = SC_DB_PREFIX.'admin_in_outs';    
    protected $connection = SC_CONNECTION;

    protected $fillable = ['type','amount','description', 'category', 'date'];
    
    CONST DEBIT_TYPE='debit';
    CONST CREDIT_TYPE='credit';

    protected $dates = ['date'];
}
