@extends('frontEnd.layouts.new_app')
@section('title','Store Listing')
@section('content')
@include('sweetalert::alert')
<?php
$category_id = Session::get('category_id');
$categoryData = DB::table('categories')->where('id',$category_id)->first();
// echo "<pre>";print_r($categoryData);exit();
?>
<main class="site-content">
    <div class="bread-crumb-block">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{ route('frontend.home') }}" class="text-grey body-normal">Home</a></li>
                <li><p class="text-black body-normal">Store</p></li>
            </ul>
        </div>
    </div>
    <section class="store-listing pt-40 py-80">
        <div class="container">
            <div class="store-listing-heading">
                <h1 class="mb-0">Search For "{{@$categoryData->title}}"</h1>
                <!-- <form action="" class="search-store" id="search-store">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" placeholder="Search store" name="search" id="search">
                </form> -->
            </div>
            <div id="storeList">            
            @include('frontEnd.stores.store_list')            
            </div>
            {{-- <ul class="pagination mb-0">
                <li class="prev disabled"><a href="">Prev</a></li>
                <li><a href="" class="active">01</a></li>
                <li><a href="">02</a></li>
                <li><a href="">03</a></li>
                <li><a href="">04</a></li>
                <li><a href="">05</a></li>
                <li class="inbetween">....</li>
                <li><a href="">24</a></li>
                <li class="next"><a href="">Next</a></li>
            </ul> --}}
        </div>
    </section>
</main>

@endsection

@section('scripts')
<script>
$('#search').keyup(function(event){
    event.preventDefault(); 
    // var page = $('.pagination a').attr('href').split('page=')[1];
    fetch_data(1);
});

$(document).on('click', '.pagination a', function(event){
    event.preventDefault(); 
    var page = $(this).attr('href').split('page=')[1];
    fetch_data(page);
});    

function fetch_data(page)
{
    $.ajax({
        type: "GET",
        url:"{{ url('/store-listing/online/') }}?page="+page,
        data: $('#search-store').serialize(),
        success: function(data){
            $('#storeList').empty();
            $('#storeList').html(data);           
        },    
        dataType: "html"
    });
}
</script>    
@endsection