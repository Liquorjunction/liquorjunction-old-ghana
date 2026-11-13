$(document).ready(function(){
    var base_url = window.location.origin;
    $("#search-box, #search-box-click").keyup(function() {
        Search();
    });

    function Search(){  
       // $('body').addClass('scrollidisable');  
        var keyword = $("#search-box").val();  
        action_url = base_url+'/search-auto-suggestion';
       // var csrf = "{{ csrf_token() }}";
        if(keyword!=''){
            $.ajax({            
                url: action_url,
                data: {'keyword':keyword}, 
                // headers: {
                //                 'X-CSRF-TOKEN': csrf
                //             },
                type: "POST",     
                beforeSend: function() {
                    // $("#search-box").css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
                },
                success: function(response) {
                // return false; 
                        if(response){
                            $('.suggesstion-box').css('border','1px solid rgba(114, 106, 106, 0.2)');
                            // $('.suggesstion-box').css('display','none');
                            $(".suggesstion-box").removeClass("d-none")
                            var outputText = '<ul id="auto-box">'+response+'</ul>';
                            $("#suggesstion-box").html(outputText);
                        }
                        else{
                            $(".suggesstion-box").removeClass("d-none")
                            // $('.suggesstion-box').css('display','none');
                            var outputText = '<ul id="auto-box">';  
                            var divHtml ='<li><a href="javascript::void(0);">No results found.</a> </li>';
                                outputText += divHtml; 
                            outputText += '</ul>';                           
                            $("#suggesstion-box").html(outputText);
                        }
                }
            });
        }else{
             $('.suggesstion-box').css('border','none');
            $("#suggesstion-box").html('');
        }
    };
});

$("#search-box").keydown(function (e) {
    if (e.keyCode == 40) {    
        
        $("#auto-box li:eq(0)").addClass('active').children('a').focus();    
        $('#auto-box').animate({scrollTop: '0px'}, 1000);
        // var el = document.querySelector('#auto-box');
        // //el.scrollTop = el.scrollHeight;
        // setTimeout(function(){
        // el.scrollTop = 0;
        // }, 50);   
    }
});

$("body #suggesstion-box").keydown(function (e) {
    //$("#auto-box li:eq(0)").addClass('active').children('a').focus();
    var total_li_count = $("#suggesstion-box ul li").length ;
    if (e.which == 40) {
        var data_class = $("#suggesstion-box .active").attr('data-index');
        var tli = parseInt(total_li_count-1);
        if(tli == data_class  ){
            $('#auto-box li:eq('+tli+')').addClass('active').children('a').focus();
        }else{
            var next = $('.active').removeClass('active').next('li');
            next = next.length > 0 ? next : $('.focus li:eq(0)');
            next.addClass('active').children('a').focus();
        }       
    }    
});

$("body #suggesstion-box").keyup(function (e) {
    var data_class = $("#suggesstion-box .active").attr('data-index');
    if (e.which == 38) {
        if(data_class==0){
            $("#auto-box li:eq(0)").addClass('active').children('a').focus();
        }else{
            var prev = $('.active').removeClass('active').prev('li');
            prev = prev.length > 0 ? prev : $('.focus li').last();
            prev.addClass('active').children('a').focus();
        }
    }
});

$('body').click(function(e) {
    if (!$(e.target).closest('#search-box').length) {
        if ($("#search-box").length){
            $("#search-box").val('');
            $("#suggesstion-box").css('border','none').html('');
        }
    }
});