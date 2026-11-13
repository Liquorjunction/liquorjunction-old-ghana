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
                                       <!--  <th> No. </th> -->
                                        <th> Instructor Name </th>
                                        <th> Requested Date </th>
                                        <th> Requested Amount </th>
                                        <th> Account Balance </th>
                                        <th> Status </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($totalAr as $key => $data)
                                        <tr>
                                            <?php $setting = App\Models\Setting::find(1);?>
                                           <!--  <th scope="row">{{ ++$key }}</th> -->
                                            <?php $u_name = App\Models\MainUser::where('id',$data->instructor_id)->first();
                                                 $uname = $u_name->name; ?>
                                            <td> {{$uname}} </td>
                                            <?php 
                                            // $date = \Helper::formatDatetime($data->created_at) . ' ' . date('H:i:s', strtotime($data->created_at)) 

                                            $createddate = \Helper::converttimeTozone($data->created_at);?>
                                            
                                            <td> {{ $createddate }} </td>
                                            <td>{{ $data->amount.'.00' }}</td>
                                            <td>{{ $data->balance.'.00' }}</td>
                                            @if($data->request_status == 0)
                                                <td> Requested </td>
                                            @elseif($data->request_status == 1)
                                                <td> Paid </td>
                                            @else
                                                <td> Denied </td>
                                            @endif
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