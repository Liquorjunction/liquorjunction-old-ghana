<! DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta http-equiv = "X-UA-Compatible" content = "IE = edge">
    <meta name = "viewport" content = "width = device-width, initial-scale = 1.0">
    <title> OnlyDance </title>
    <! - Bootstrap5 CSS ->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

   <style>
   table, th, td {
  border: 1px solid black;
}
 th, td {
  border-color: #96D4D4;
 
}
.col1{
   width:100px;
}
.col2{
   width:500px;;
}

   </style>
</head>
<body>
    <div class="padding list-school">
        <div class="box">
            
            <div class="box nav-active-border b-info">

                <div class="tab-content clear b-t">
                    <div class="tab-pane active" id="tab_details">
                        <div class="box-body">


                            <form class="table_design">


                                <table class="table table-bordered m-a-0">

                                    <thead>
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Instructor Name</th>
                                            <th>Purchase User</th>
                                            <th>Purchase Date</th>
                                            <th>Instructor Amount</th>
                                            <th>Admin Commission Amount</th>
                                            <th>Class Price</th>
                                            <!-- <th>Total Amount</th> -->
                                            <!-- <th>Total Course Purchase</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($totalAr as $key => $data)
                                        <tr>
                                            <?php $setting = App\Models\Setting::find(1);?>
                                            <!-- <th scope="row">{{ ++$key }}</th> -->
                                            {{-- <?php $aa = public_path('uploads/website_users/'.$data->profile); ?>
                                            <td><img src="<?php echo $aa; ?>" class="thumbnail" width="100px" height="100px"/></td> --}}
                                            <?php $cl_name = App\Models\DanceClass::where('id',$data->class_id)->first();
                                                 $cname = $cl_name->class_name; 
                                                 $cprice = $cl_name->price; ?>
                                            <td> {{$cname}} </td>
                                            <?php $u_name = App\Models\MainUser::where('id',$data->user_id)->first();
                                                 $uname = $u_name->name; 
                                                 $p_name = App\Models\MainUser::where('id',$data->purchase_user_id)->first();
                                                 $pname = $p_name->name; ?>
                                            <td> {{$uname}} </td>
                                            <td> {{$pname}} </td>
                                            <?php 
                                            // $date = \Helper::formatDatetime($data->purchase_date) . ' ' . date('H:i:s', strtotime($data->purchase_date)) 

                                            $createddate = \Helper::converttimeTozone($data->purchase_date);?>
                                            <td> {{ $createddate }} </td>
                                            <td>{{ $data->instructor_amount.'.00' }}</td>
                                            <td>{{ $data->admin_commission_amount.'.00' }}</td>
                                            <td>{{ $cprice.'.00' }}</td>
                                            <!-- <td>{{ $data->total_amount.'.00' }}</td> -->
                                            <?php 
                                            $tot_class_sub = App\Models\ClassPurchaseHistory::where('purchase_user_id',$data->purchase_user_id)->count(); ?>
                                            <!-- <td> {{$tot_class_sub}} </td> -->
                                        </tr>
                                        @endforeach
                                    </tbody>


                                </table>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="{{ asset('assets/dashboard/js/jquery.validate.min.js') }} "></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
async defer></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize" async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>
</body>
</html>    