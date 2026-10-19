<! DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta http-equiv = "X-UA-Compatible" content = "IE = edge">
    <meta name = "viewport" content = "width = device-width, initial-scale = 1.0">
    <title> Medvision | Order Report </title>
    <! - Bootstrap5 CSS ->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

   <style>
   table, th, td {
  border: 1px solid black;
}
 th, td {
  border-color: #96D4D4;
 
}
/*.col1{
   width:100px;
}
.col2{
   width:500px;;
}*/

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


                               <div class="new-order ">
                                                <div class="">
                                                    <table class="table student-list-table program-manage" id="list_student">
                                                        <thead>
                                                            <tr >
                                                                
                                                                <th >
                                                                    <div class="id">
                                                                        <p>ID </p>
                                                                    </div>
                                                                </th>
                                                                <th >
                                                                    <p>Order Id</p>
                                                                </th>
                                                                <th >
                                                                    <p>Customer Name</p>
                                                                </th>
                                                                <th >
                                                                    <p>Product Name</p>
                                                                </th>
                                                                <th >
                                                                    <p>Order Date</p>
                                                                </th>
                                                               
                                                                
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($totalAr as $key => $data)
                                                             <?php 
                                                            $date = \Helper::converttimeTozone($data['date_time']);

                                                           
                                                            ?>
                                                         
                                                            <tr>
                                                                
                                                                <td>
                                                                    <div class="id">
                                                                        <p>{{$key+1}}</p>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <p>{{$data['order_id']}}</p>
                                                                </td>
                                                               
                                                                <td class="table-order-location">
                                                                    <p >{{$data['customer_first_name']}} {{$data['customer_surname']}}</p>
                                                                </td>
                                                                <td class="table-order-location">
                                                                    <p >{{$data['product_name']}}</p>
                                                                </td>
                                                                <td class="table-order-location">
                                                                    <p >{{$date}}</p>
                                                                </td>
                                                                
                                                            </tr> 
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

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