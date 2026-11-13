// For error in form 
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
	'use strict'
  
	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.querySelectorAll('.registration-form', '.get-quote-form', '.change-pass-form', '.edit-profile-form')
  
	// Loop over them and prevent submission
	Array.prototype.slice.call(forms)
	  .forEach(function (form) {
		form.addEventListener('submit', function (event) {
		  if (!form.checkValidity()) {
			event.preventDefault()
			event.stopPropagation()
		  }
  
		  form.classList.add('was-validated')
		}, false)
	})
})()

// Footer Menu One

jQuery('h5.menu_arrow').click(function() {
    if (jQuery(window).width() < 576) {
        jQuery(this).next().slideToggle(300);
        jQuery(this).toggleClass("active");
    }
});

var resizeTimer;
$(window).resize(function(e) {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        $(window).trigger('delayed-resize', e);
    }, 250);
});

// Resize Function

$(window).on("load resize", function(e) {
    if ($(window).width() > 575) {
        $(".links").show();
    } else {
        $(".links").hide();
    }
});

// Show Hiden Password
$(".toggle-password").click(function() {
	$(this).toggleClass("show-hide-password");
	input = $(this).parent().find("input");
	if (input.attr("type") == "password") {
		input.attr("type", "text");
	} else {
		input.attr("type", "password");
	}
});

// OTP
$('.digit-group').find('input').each(function() {
    $(this).attr('maxlength', 1);
    $(this).on('keyup', function(e) {
        var parent = $($(this).parent());
        if(e.keyCode === 8 || e.keyCode === 37) {
            var prev = parent.find('input#' + $(this).data('previous'));
            if(prev.length) {
                $(prev).select();
            }
        } else if((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
            var next = parent.find('input#' + $(this).data('next'));
            if(next.length) {
                $(next).select();
            } else {
                if(parent.data('autosubmit')) {
                    parent.submit();
                }
            }
        }
    });
});

// checkout Page toggle
$('.order-summary-body .toggle').on("click", function() {
  if ($(window).width()) {
      $('.cart-item-list').slideToggle();
  }
  $('.cart-item-list').toggleClass('toggled-on');
  $('.order-summary-body .toggle').toggleClass('toggled-on');
});
// End checkout Page toggle

// Sticky Header

function resizeHeaderOnScroll() {
    const distanceY = window.pageYOffset || document.documentElement.scrollTop,
       shrinkOn = 20,
       headerEl = document.getElementById('header');
    if (distanceY > shrinkOn) {
       headerEl.classList.add("smaller");
    } else {
       headerEl.classList.remove("smaller");
    }
}
 
window.addEventListener('scroll', resizeHeaderOnScroll);

// Product Swiper Slide
var swiper = new Swiper(".product-slider", {
    slidesPerView: 4,
    spaceBetween: 24,
    slidesPerGroup: 1,
    // grabCursor: true,
    // effect: "creative",
    // creativeEffect: {
    //     prev: {
    //       shadow: true,
    //       translate: [0, 0, -400],
    //     },
    //     next: {
    //       translate: ["100%", 0, 0],
    //     },
    // },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 12,
      },
      480: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 24,
      },
      992: {
        slidesPerView: 4,
        spaceBetween: 24,
      },
    },
});

// Service Swiper Slide
var swiper = new Swiper(".service-slider", {
    slidesPerView: 6,
    spaceBetween: 24,
    slidesPerGroup: 1,
    // grabCursor: true,
    // effect: "creative",
    // creativeEffect: {
    //     prev: {
    //       shadow: true,
    //       translate: [0, 0, -400],
    //     },
    //     next: {
    //       translate: ["100%", 0, 0],
    //     },
    // },
    navigation: {
        nextEl: ".swiper-button-next-service",
        prevEl: ".swiper-button-prev-service",
    },
    breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 12,
      },
      481: {
        slidesPerView: 3,
        spaceBetween: 12,
      },
      641: {
        slidesPerView: 4,
        spaceBetween: 16,
      },
      768: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      992: {
        slidesPerView: 5,
        spaceBetween: 24,
      },
      1200: {
        slidesPerView: 6,
        spaceBetween: 24,
      },
    },
});

// Product Details Slide
var swiper = new Swiper(".product-details-slider", {
  slidesPerView: 1,
  spaceBetween: 10,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    481: {
      slidesPerView: 2,
      spaceBetween: 16,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 16,
    },
    1024: {
      slidesPerView: 4,
      spaceBetween: 24,
    },
  },
});

// Upload Photo Popup
let fileInput = document.getElementById("file-upload-input");
let fileSelect = document.getElementsByClassName("file-upload-select")[0];
fileSelect.onclick = function() {
	fileInput.click();
}
fileInput.onchange = function() {
	let filename = fileInput.files[0].name;
	let selectName = document.getElementsByClassName("file-select-name")[0];
	selectName.innerText = filename;
}

// Counter
$(document).ready(function() {

  // ------------ Counter BEGIN ------------ 
  $(".counter__increment, .counter__decrement").click(function(e)
  {
    // alert('ehllo')
    var $this = $(this);
    var $counter__input = $(this).parent().find(".counter__input");
    var $currentVal = parseInt($(this).parent().find(".counter__input").val());

    //Increment
    if ($currentVal != NaN && $this.hasClass('counter__increment'))
    {
        // alert('hello25')
      $counter__input.val($currentVal + 1);
    }
    //Decrement
    else if ($currentVal != NaN && $this.hasClass('counter__decrement'))
    {
        // alert('hello')
      // if ($currentVal >= 1) {
      //   alert($currentVal)
      // }

      if ($currentVal == 1) {
        document.getElementsByClassName("counter__decrement").disabled = true;
      }else{

        $counter__input.val($currentVal - 1);
      }
    }
  });
  // ------------ Counter END ------------ 

});