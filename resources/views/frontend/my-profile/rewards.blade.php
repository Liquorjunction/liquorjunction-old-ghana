@extends('frontend.layouts.app')
@section('title', 'My Reward')
@section('content')
@include('sweetalert::alert')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap4.css"> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.min.css">


<style>
   
    .dt-paging {
        margin-top: 20px;
        text-align: center;
    }

    .dt-paging ul.pagination {
        display: inline-flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .dt-paging ul.pagination li.page-item {
        margin: 0 3px;
    }

    .dt-paging ul.pagination li.page-item button.page-link {
        background-color: #000000; /* solid black */
        color: white;
        border: none;
        border-radius: 5px;
        padding: 6px 14px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
        min-width: 40px;
        text-align: center;
    }

    .dt-paging ul.pagination li.page-item.disabled button.page-link {
        background-color: #d3d3d3; /* light gray */
        color: #888888; /* medium gray text */
        cursor: not-allowed;
    }

    .dt-paging ul.pagination li.page-item.active button.page-link {
        background-color: #222222; /* dark gray (lighter than black) */
        color: white;
        cursor: default;
    }

    .dt-paging ul.pagination li.page-item button.page-link:hover:not(:disabled):not(.active) {
        background-color: #555555; /* medium gray on hover */
        color: white;
    }



    /* Search Box */
    .dt-search input[type="search"] {
    border-radius: 15px; 
    padding: 6px 12px;   
    border: 1px solid #ccc; 
    transition: border-color 0.3s ease;
    }

    .dt-search input[type="search"]:focus {
        outline: none;
        border-color: #000; 
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }


    /* table alignment */
    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left !important;
        }

    /* Data table design ends */
    .reward-header {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 20px;
      font-weight: bold;
    }

    .reward-header img {
      width: 40px;
      height: 40px;
    }

    .reward-points {
      color: #2e7d32;
      font-size: 18px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
</style>

<div class="bread-crumb-block">
    <div class="container">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('frontend.home')}}">{{@Helper::language('home')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Reward Points</li>
        </ul>
    </div>
</div>

<section class="account pt-20 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4">
            @include('frontend.layouts.account-sidebar')
            </div>
            <div class="col-lg-9 col-md-8">
                <h2>My Reward</h2>  
                <div class="reward-header pt-3">
                    <img src="https://img.icons8.com/color/48/trophy.png" alt="Trophy Icon">
                    <span>{{ $totalPoints}} <span class="reward-points">Reward Points</span></span>
                </div>
                <p>Use Reward Point on purchase</p>

                <h4>Last Transactions</h4>

                   <table id="rewardsTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>Sr no.</th>
                        <th>Order Id</th>
                        <th>Spent Points</th>
                        <th>Earned Points</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($points as $data)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $data->order_id }}</td>
                            
                            {{-- Conditional rendering of points --}}
                            <td>
                                @if(strtolower($data->type) === 'debit')
                                    <span style="color: red;">-{{ $data->points }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(strtolower($data->type) === 'credit')
                                    <span style="color: green;">+{{ $data->points }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
{{-- <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap4.js"></script> --}}
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.min.js"></script>
<script>
   new DataTable('#rewardsTable', {
    ordering: false,
    lengthChange: false,
    info: true,
    paging: true,  
    searching: true,  
    pageLength: 5 
});
</script>

@endsection
