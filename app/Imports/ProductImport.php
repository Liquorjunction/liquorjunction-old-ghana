<?php

namespace App\Imports;

use App\Models\Product;
use DB;
use Auth;
use Storage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    private $uploadPath = "uploads/product";
    public function getUploadPath()
    {
        return $this->uploadPath;
    }
    public function collection(Collection $row)
    {
        $column = $row->toArray(); 
         $supplier_id = auth()->guard('main_user')->user()->id;
        // echo "<pre>";print_r($supplier_id);exit();
        if($column[0][0] != 'Category' || $column[0][1] != 'Product Name' || $column[0][2] != 'Short Description' || $column[0][3] != 'Retail Price' || $column[0][4] != 'Discounted Price' || $column[0][5] != 'Long Description' || $column[0][6] != 'Product Image url' || $column[0][7] != 'Tech data sheet URL')
        { 
        // echo "<pre>";print_r($column);exit();
            return $this->no_data = 0 ; 
        }
        // echo "<pre>";print_r($column);

        unset($column[0]); 

        foreach ($column as $key => $row1) {
            // echo "test:<pre>";print_r($row1);
            $categoryData = DB::table('categories')->where('status',1);
                $searchValue1 = $row1[0];
                $categoryData = $categoryData->where(function ($query) use ($searchValue1) {
                 $query->orWhere('categories.title', 'like',$searchValue1);
                });

                $categoryData = $categoryData->first();
                // echo "<pre>";print_r($categoryData);
                if (empty($categoryData->id)) {
                    
                    return $this->no_data = -1 ;
                }

            if ($row1[3] > $row1[4]) {
               
            }else{
                return $this->no_data = 2 ;
            }
        }
        // exit();
        foreach ($column as $key => $row) 
        { 
            if ($row != null) {

                $url=$row[6];
                $filename = basename($url);
                $path = $this->getUploadPath();
                // echo "<pre>";print_r($filename);exit();
                $data = file_get_contents($url);
                Storage::put($path, $data);
                // $filename->move($path, $filename);

                if ($row[7] != '') {
                    
                    $url1=$row[7];
                    $filename1 = basename($url1);
                    $path1 = $this->getUploadPath();
                    // echo "<pre>";print_r($filename);exit();
                    $data1 = file_get_contents($url1);
                    Storage::put($path1, $data1);
                }

                $categoryData = DB::table('categories')->where('status',1);
                $searchValue = $row[0];
                $categoryData = $categoryData->where(function ($query) use ($searchValue) {
                 $query->orWhere('categories.title', 'like',$searchValue);
                });

                $categoryData = $categoryData->first();
                $uniqid = uniqid();
                // echo "<pre>";print_r($categoryData);exit();
                $product = new Product();
                $product->uniqid = $uniqid;
                $product->category_id = $categoryData->id;
                $product->supplier_id = $supplier_id;
                $product->product_name = $row[1];
                $product->short_description = $row[2];
                $product->retail_price = $row[3];
                $product->discount_price = @$row[4];
                $product->description = $row[5];
                $product->product_image = $filename;
                $product->tech_data_sheet = @$filename1;
                $product->in_online = 1;
                $product->in_store = 1;
                $product->status = 1;
                $product->is_admin_approve = 0;
                $product->save();

                $number = str_pad($product->id,6,'0',STR_PAD_LEFT);
                $product_item_id = 'PRO'.$number;

                $updatepsw = Product::where('id',$product->id)->update(array(
                           'product_item_id' => @$product_item_id,
                        ));

            }
        }
        
        $this->no_data = 1;
        // exit();
        return $this->no_data;
    }
}
