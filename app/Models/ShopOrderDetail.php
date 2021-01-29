<?php
#app/Models/ShopOrderDetail.php
namespace App\Models;

use App\Http\Controllers\ShopCart;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Model;
use DB;

class ShopOrderDetail extends Model
{
    protected $table = SC_DB_PREFIX.'shop_order_detail';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function updateDetail($id, $data)
    {
        return $this->where('id', $id)->update($data);
    }
    public function addNewDetail($data)
    {
        DB::beginTransaction();

        if ($data) {
            $this->insert($data);
            //Update stock, sold
            foreach ($data as $key => $item) {
                $product = (new ShopProduct)->getDetail($item['product_id']);                
                $qty = $item['qty'];
                $product_id = $item['product_id'];

                if(!ShopCart::validateStock($product_id, $qty)){
                    DB::rollback();
                    throw new \Exception("No hay inventario suficiente para el producto " . $item['name'], 1);                    
                }

                if($product->lower_price > floatval($item['price'])) {
                    DB::rollback();
                    throw new \Exception("El precio de venta es menor al precio minimo: " . $item['name'], 1);                    
                }

                ShopProduct::updateStock($product_id, $qty);

            }
        }
        DB::commit();
        return;
    }
}
