<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE = edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1.0">
    <title> Talat </title>
    <!--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> -->

    <style>
        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            border-color: #96D4D4;

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
                                <div class="new-order ">
                                    <div class="">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <p>No</p>
                                                    </th>
                                                    <th>
                                                        <p>Transaction ID</p>
                                                    </th>
                                                    <th>
                                                        <p>Transaction Date/Time</p>
                                                    </th>
                                                    <th>
                                                        <p>Customer Name</p>
                                                    </th>
                                                    <th>
                                                        <p>Service Provider Name</p>
                                                    </th>
                                                    <th>
                                                        <p>Total Amount
                                                        </p>
                                                    </th>
                                                    <th>
                                                        <p>Status
                                                        </p>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // $trans_date= \Helper::converttimeTozone($totalAr->trans_date);
                                                // $setting = \Setting::find(1);
                                                // $amount=$setting->currency.$totalAr->trans_amount;
                                                ?>
                                                @foreach ($totalAr as $key => $data)
                                                    <tr>
                                                        <td>
                                                            <p>{{ $key + 1 }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ $data['transaction_no'] }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ Helper::converttimeTozone($data['trans_date']) }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ $data['user_name'] }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ $data['provider_name'] }}</p>
                                                        </td>
                                                        <td>
                                                            <p>{{ number_format($data['trans_amount'], 2) }}</p>
                                                        </td>
                                                        <td>
                                                            <p> {{ $data['trans_status'] }}</p>
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
    <!-- <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
        async defer></script> -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC3ksZwnrjrnMtiZXZJ7cx9YEckAlt3vh4&libraries=places&callback=initialize"
        async defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript">
        $(".tab-content :input").prop("disabled", true);
    </script>
</body>

</html>
