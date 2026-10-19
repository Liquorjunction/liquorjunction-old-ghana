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
                                            <th>User Type</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Created Date/Time</th>
                                            <th>Dance Category</th>
                                            <th>Total Class Added</th>
                                            <th>Total Earns from Platform</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($totalAr as $key => $data)
                                        <tr>
                                            <?php $setting = App\Models\Setting::find(1);?>
                                            @if($data->user_type == 2)
                                                <td>Normal</td>
                                            @elseif($data->user_type == 3)
                                                <td>Instructor</td>
                                            @else
                                                <td>Admin</td>
                                            @endif
                                            <!-- <th scope="row">{{ ++$key }}</th> -->
                                            {{-- <?php $aa = public_path('uploads/website_users/'.$data->profile); ?>
                                            <td><img src="<?php echo $aa; ?>" class="thumbnail" width="100px" height="100px"/></td> --}}
                                            <td> {{$data->name}} </td>
                                            <td> {{$data->email}} </td>
                                            <td> {{"+".$data->country_code.' '.$data->phone}} </td>
                                            <?php 
                                            // $date = \Helper::formatDatetime($data->created_at) . ' ' . date('H:i:s', strtotime($data->created_at)) 
                                            
                                            $createddate = \Helper::converttimeTozone($data->created_at);
                                            ?>
                                            <td> {{ $createddate }} </td>
                                            <?php 
                                            $dance_class = App\Models\DanceClass::where('id',$data->class_id)->first(); 
                                            $dance_category = App\Models\DanceCategory::where('id',$dance_class->dance_category_id)->first();?>
                                            <td> {{$dance_category->category_name}} </td>
                                            <?php 
                                            $tot_class_sub = App\Models\ClassPurchaseHistory::where('user_id',$data->user_id)->count(); ?>
                                            <td> {{$tot_class_sub}} </td>
                                            <?php $tot_amt = \DB::table('class_purchase_history')
                                            ->where('user_id',$data->user_id)
                                            ->sum('instructor_amount'); ?>
                                            <td> {{$tot_amt.'.00'}} </td>
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