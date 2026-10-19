<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\OrderDetails;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $row)
    {
        $column = $row->toArray(); 
        
        if($column[0][0] != 'Product ID' || $column[0][1] != 'Order ID' || $column[0][2] != 'Quantity' || $column[0][3] != 'Total Amount')
        { 
            return $this->no_data = 0 ; 
        }

        unset($column[0]); 
        // echo "<pre>";print_r($column);exit();
        foreach ($column as $key => $row) 
        {
            $productData = DB::table('product')->where('product_item_id',$row[0])->first();
            //dd($productData->discount_price);

            if (empty($productData)) {
                return $this->no_data = -2 ;
            }

            $amount = isset($productData->discount_price) ? $productData->discount_price : $productData->retail_price;

            if($row[3] != $amount)
            {
                return $this->no_data = -5 ;
            }

            $orderData = DB::table('order')->where('order_id',$row[1])->first();

            if (empty($orderData)) {
                return $this->no_data = -3 ;
            }

            if($row[3] == '0')
            {
                return $this->no_data = -4 ;
            }
        }

        //$orderData = DB::table('order')->where('order_id',$row[1])->first();
        $payableamount = 0;
        foreach ($column as $key => $row) 
        { 
            
            // echo "<pre>";print_r($row);exit();
            if ($row != null) {
            $productData = DB::table('product')->where('product_item_id',$row[0])->first();

            // echo "<pre>";print_r($row[1]);
            // echo "<pre>";print_r($productData);exit();
            
            $orderdetailData = DB::table('order_detail')->where('product_id',$productData->id)->where('order_id',$orderData->id)->delete();
            // echo "productData:<pre>";print_r($productData);
            // echo "orderData:<pre>";print_r($orderData);
                $order_detail = new OrderDetails();
                $order_detail->order_id = $orderData->id;
                $order_detail->product_id = $productData->id;
                $order_detail->quantity = $row[2];
                $order_detail->supplier_id = $productData->supplier_id;
                $order_detail->status = 1;

                $order_detail->save();
                // echo "<pre>";print_r($order_detail->id);exit();
                //now product wise price add and sum of total amount is payable amount order id wise
                $amount = $row[2] * $row[3];   
                $payableamount += $amount;
            }
        }
        $updatepsw = Order::where('id',$orderData->id)->update(array(
                   'order_type' => 3,
                   'total_amount' => $payableamount,
                   'payable_amount' => $payableamount,
                   'order_status' => 2
        ));
        $this->no_data = 1;
        // exit();
        return $this->no_data;
    }
}
