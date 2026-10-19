function getVals(){
  // Get slider values
  let parent = this.parentNode;
  let slides = parent.getElementsByTagName("input");
    let slide1 = parseFloat( slides[0].value );
    let slide2 = parseFloat( slides[1].value );
  // Neither slider will clip the other, so make sure we determine which is larger
  if( slide1 > slide2 ){ let tmp = slide2; slide2 = slide1; slide1 = tmp; }
  
  let displayElement = parent.getElementsByClassName("rangeValues")[0];
      displayElement.innerHTML = "$" + slide1;
  let displayElement1 = parent.getElementsByClassName("rangeValues1")[0];
      displayElement1.innerHTML = "$" + slide2;
}

window.onload = function(){
  // Initialize Sliders
  let sliderSections = document.getElementsByClassName("range-slider");
      for( let x = 0; x < sliderSections.length; x++ ){
        let sliders = sliderSections[x].getElementsByTagName("input");
        for( let y = 0; y < sliders.length; y++ ){
          if( sliders[y].type ==="range" ){
            sliders[y].oninput = getVals;
            // Manually trigger event first time to display values
            sliders[y].oninput();
          }
        }
      }
}

/* Sticky Header */
function resizeHeaderOnScroll() {
  const distanceY = window.pageYOffset || document.documentElement.scrollTop,
    shrinkOn = 50,
    headerEl = document.getElementById('masthead');
    if (distanceY > shrinkOn) {
      headerEl.classList.add("smaller");
    } else {
      headerEl.classList.remove("smaller");
    }
}
window.addEventListener('scroll', resizeHeaderOnScroll);
/* End Sticky Header */

/* Counter */
jQuery('.count').each(function() {
    jQuery(this).prop('Counter', 0).animate({
        Counter: jQuery(this).text()
    }, {
        duration: 3000,
        easing: 'swing',
        step: function(now) {
            jQuery(this).text(Math.ceil(now));
        }
    });
});
/* End Counter */

/* Banner Slider */ 
var swiper = new Swiper(".banner-slider", {
    autoplay: false,
    autoHeight: true,
    slidesPerView: 1,
    spaceBetween: 0,
    speed: 1000,
    loop: false,
    navigation: {
      prevEl: ".banner-button-prev",
      nextEl: ".banner-button-next",
    }
});
/* End Banner Slider */

/* Shop Spirit Slider */
var swiper = new Swiper(".shop-spirit-slider", {
  slidesPerView: 7,
  spaceBetween: 70,
  autoHeight: true,
  speed: 2000,
  loop: true,
  navigation: {
    prevEl: ".shop-spirit-button-prev",
    nextEl: ".shop-spirit-button-next",
  },
  breakpoints: {
    "@0.00": {
      slidesPerView: 3,
      spaceBetween: 30,
    },
    // "481": {
    //   slidesPerView: 3
    // },
    "576": {
      slidesPerView: 4
    },
    "768": {
      slidesPerView: 5
    },
    "992": {
      slidesPerView: 6
    },
    "1200": {
      slidesPerView: 7
    },
  },
  scrollbar: {
    el: ".shop-spirit-scrollbar",
  },
});
/* End Shop Spirit Slider */

/* Our Highlights Slider */
var swiper = new Swiper(".our-highlights-slider", {
  spaceBetween: 25,
  autoHeight: true,
  speed: 2000,
  navigation: {
    prevEl: ".our-highlights-button-prev",
    nextEl: ".our-highlights-button-next",
  },
  breakpoints: {
    "@0.00": {
      slidesPerView: 1
    },
    "576": {
      slidesPerView: 2
    },
  },
  scrollbar: {
    el: ".highlights-scrollbar",
  },
});
/* End Our Highlights Slider */

/* Offer Slider */
var swiper = new Swiper(".offers-slider", {
  spaceBetween: 24,
  autoHeight: true,
  speed: 2000,
  loop: true,
  navigation: {
    prevEl: ".offers-button-prev",
    nextEl: ".offers-button-next",
  },
  breakpoints: {
    "@0.00": {
      slidesPerView: 1,
      spaceBetween: 16,
    },
    "481": {
      slidesPerView: 2,
    },
    // "768": {
    //   slidesPerView: 3,
    // },
    "840": {
      slidesPerView: 3,
      spaceBetween: 24,
    },
    "1200": {
      slidesPerView: 4,
    },
  },
  scrollbar: {
    el: ".offers-scrollbar",
  },
});
/* End Offer Slider */

/* Best Seller Slider */
var swiper = new Swiper(".best-seller-slider", {
  spaceBetween: 24,
  autoHeight: true,
  grabCursor: true,
  speed: 2000,
  navigation: {
    prevEl: ".best-seller-button-prev",
    nextEl: ".best-seller-button-next",
  },
  breakpoints: {
    "@0.00": {
      slidesPerView: 1,
      spaceBetween: 16,
    },
    "481": {
      slidesPerView: 2,
    },
    // "768": {
    //   slidesPerView: 3,
    // },
    "840": {
      slidesPerView: 3,
      spaceBetween: 24,
    },
    "1200": {
      slidesPerView: 4,
    },
  },
  scrollbar: {
    el: ".best-seller-scrollbar",
  },
});
/* End Best Seller Slider */

/* Recently Viewed Slider */
var swiper = new Swiper(".recently-viewed-slider", {
  spaceBetween: 24,
  autoHeight: true,
  grabCursor: true,
  speed: 2000,
  navigation: {
    prevEl: ".recently-viewed-button-prev",
    nextEl: ".recently-viewed-button-next",
  },
  breakpoints: {
    "@0.00": {
      slidesPerView: 1
    },
    "381": {
      slidesPerView: 2
    },
    "768": {
      slidesPerView: 3,
    },
    "1024": {
      slidesPerView: 4,
    },
  },
  scrollbar: {
    el: ".recently-viewed-scrollbar",
  },
});
/* End Recently Viewed Slider */

/* Top Selling Slider */
var swiper = new Swiper(".top-selling-slider", {
  spaceBetween: 24,
  autoHeight: true,
  grabCursor: true,
  speed: 2000,
  navigation: {
    prevEl: ".top-selling-button-prev",
    nextEl: ".top-selling-button-next",
  },
  breakpoints: {
    "@0.00": {
      slidesPerView: 1
    },
    "381": {
      slidesPerView: 2
    },
    "768": {
      slidesPerView: 3,
    },
    "1024": {
      slidesPerView: 4,
    },
  },
  scrollbar: {
    el: ".top-selling-scrollbar",
  },
});
/* End Top Selling Slider */

/* Awards Slider */ 
var swiper = new Swiper(".awards-slider", {
    autoplay: {
      delay: 1,
    },
    autoHeight: false,
    slidesPerView: 7,
    spaceBetween: 60,
    speed: 4000,
    slidesPerView:'auto',
    allowTouchMove: false,
    disableOnInteraction: true,
    loop: true,
    breakpoints: {
        "@0.00": {
          slidesPerView: 3,
          spaceBetween: 20,
        },
        "576": {
          slidesPerView: 4,
          spaceBetween: 30,
        },
        "768": {
          slidesPerView: 5,
          spaceBetween: 40,
        },
        "992": {
          slidesPerView: 6,
          spaceBetween: 50,
        },
        "1024": {
          slidesPerView: 7,
          spaceBetween: 60,
        },
    },
});
/* End Awards Slider */

// Product Details Slide
var swiper = new Swiper(".product-details-slider-mobile", {
  slidesPerView: 1,
  spaceBetween: 10,
  navigation: {
    nextEl: ".product-detail-next-mobile",
    prevEl: ".product-detail-prev-mobile",
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
// End Product Details Slide

// CMS slider
var swiper = new Swiper(".cms-swiper", {
  slidesPerView: 1,
  spaceBetween: 16,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
  loop: true,
  breakpoints: {
    481: {
      slidesPerView: 2,
      spaceBetween: 16,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 16,
    },
    992: {
      slidesPerView: 4
    },
  },
});
// End CMS slider

// Show Hide switch toggle block JS
$(document).ready(function () {
  $(".radioPurchase").on("change", function () {
    var val = $(this).attr("data-class");
    $(".purchase-main").hide();
    $("." + val).show();
  });
});
$(document).ready(function () {
  $(".radioCard").on("change", function () {
    var val = $(this).attr("data-class");
    $(".radioCardShow").hide();
    $("." + val).show();
  });
});
$(document).ready(function () {
  $(".addressRadioCurrent").on("change", function () {
    var val = $(this).attr("data-class");
    $(".addressRadioCurrentShow").hide();
    $("." + val).show();
  });
});

// checkout Page toggle
$('.convenience-row.toggle').on("click", function() {
  if ($(window).width()) {
    $('.convenience-inner').slideToggle();
  }
  $('.convenience-inner').toggleClass('toggled-on');
  $('.convenience-row.toggle').toggleClass('toggled-on');
});

$('.order-summary-body .toggle1').on("click", function() {
  if ($(window).width()) {
      $('.cart-item-list').slideToggle();
  }
  $('.cart-item-list').toggleClass('toggled-on');
  $('.order-summary-body .toggle1').toggleClass('toggled-on');
});
// End checkout Page toggle

/* go-to-top */

var body = $('body'),
    _window = $(window);

$(function() {
    // Amount of scrolling before button is shown/hidden.
    var offset = 100;

    // Fade duration
    var duration = 1000;

    // Toggle view of button when scrolling.
    $(window).scroll(function() {
        if ($(this).scrollTop() > offset) {
            $('#c-go-top').fadeIn(duration);
        } else {
            $('#c-go-top').fadeOut(duration);
        }
    });

    // Scroll to top when button is clicked.
    $('#c-go-top').click(function(event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, duration);
        return false;
    });
});

/* Footer Menu */

jQuery('.footer-link h6').click(function() {
  if (jQuery(window).width() < 768) {
      jQuery(this).next().slideToggle(300);
      jQuery(this).toggleClass("active");
  }
});

var resizeTimer;
jQuery(window).resize(function(e) {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
      jQuery(window).trigger('delayed-resize', e);
  }, 250);
});

// Resize Function Filter

jQuery(window).on("load resize", function(e) {
  if (jQuery(window).width() > 1920) {
      jQuery(".filter-list").show();
  } else {
      jQuery(".filter-list").hide();
  }
});

/* Footer Menu */
jQuery('.filter-listing .filter-title').click(function() {
  if (jQuery(window).width() < 1921) {
      jQuery(this).next().slideToggle(300);
      jQuery(this).toggleClass("active");
  }
});

var resizeTimer;
jQuery(window).resize(function(e) {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
      jQuery(window).trigger('delayed-resize', e);
  }, 250);
});

// Resize Function
jQuery(window).on("load resize", function(e) {
  if (jQuery(window).width() > 767) {
      jQuery(".links").show();
  } else {
      jQuery(".links").hide();
  }
});

/* Add Active state in Menu link JS */
jQuery(function($) {
  var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
  jQuery('.menu .menu-item > a').each(function() {
    if (this.href === path) {
      jQuery(this).addClass('active');
    }
  });
});

// Add and Remove class from body

$('.backdrop').on('click', function(e) { 
  $('body').addClass('scrollidisable');
  e.stopPropagation();
});

$('html').on('click', function(e) {
  $('body').removeClass('scrollidisable');
  e.stopPropagation();
});

// Mobile Header Search Bar Js
$('.search-icon').on("click",function(){
  if ($(window).width()) {
      $('.searchForm').slideToggle();
  }
  $('.searchForm').toggleClass('toggled-on');
});
// End Mobile Header Search Bar Js

window.addEventListener('scroll', resizeHeaderOnScroll);

// Show more OR Load more 

$(document).ready(function() {
  $('.product-listing-col').hide();
  $('.product-listing-col').each(function(index, value) {
      if (index < 8) {
          $(this).show();
      }
  });
  if ($('.product-listing-col:hidden').length) {
      $('.show-more').show();
  }
  if (!$('.product-listing-col:hidden').length) {
      $('.show-more').hide();
  }

});

$('.show-more').on('click', function() {
  $('.product-listing-col:hidden').each(function(index, value) {
      if (index < 8) {
          $(this).show();
      }
  });
  if (!$('.product-listing-col:hidden').length) {
      $('.show-more').hide();
  }
});

// Counter
$(document).ready(function() {

  // ------------ Counter BEGIN ------------ 
  $(".counter__increment, .counter__decrement").click(function(e)
  {
    var $this = $(this);
    var $counter__input = $(this).parent().find(".counter__input");
    var $currentVal = parseInt($(this).parent().find(".counter__input").val());

    //Increment
    if ($currentVal != NaN && $this.hasClass('counter__increment'))
    {
      $counter__input.val($currentVal + 1);
    }
    //Decrement
    else if ($currentVal != NaN && $this.hasClass('counter__decrement'))
    {
      if ($currentVal >= 1) {
        $counter__input.val($currentVal - 1);
      }
    }
  });
  // ------------ Counter END ------------ 

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

// Read More
$(".read-more").click(function () {
	$(".hidden-content").slideToggle();
	if ($(".read-more").text() == "Read Less") {
	  $(this).text("Read More");
	} else {
	  $(this).text("Read Less");
	}
  });
  
 $('.read-more').click(function(){	
	if ($(window).width()) {
		$(this).next().slideToggle(300);
		$(this).toggleClass("active");
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

// Product Detail Slider
var swiperProduct = new Swiper(".product-detail-thumb", {
  spaceBetween: 16,
  slidesPerView: 3,
  freeMode: true,
  watchSlidesProgress: true,
});
var swiper2 = new Swiper(".product-detail", {
  spaceBetween: 16,
  navigation: {
    nextEl: ".product-detail-next",
    prevEl: ".product-detail-prev",
  },
  thumbs: {
    swiper: swiperProduct,
  },
});

// On scroll load data

$(document).ready(function () {
  const productsPerPage = 20;
  let currentPage = 1;
  let isLoading = false;

  // Function to show the loader text
  function showLoader() {
    $('#loader').removeClass('hidden');
}

// Function to hide the loader text
function hideLoader() {
    $('#loader').addClass('hidden');
}

  function loadProducts() {
      if (isLoading) return;

      isLoading = true;

      // Show loader text while products are being fetched
      showLoader();


      // Simulate an AJAX request to retrieve the next set of products.
      // Replace this with your actual AJAX request to fetch products from your server.
      const fakeApiResponse = {
          products: []
      };

      const blogBox = ` <div class="blog-box">
            <div class="blog-image">
                <img src="images/blog-img1.jpg" alt="blog-img1" title="blog-img1" />
            </div>
            <div class="blog-content">
                <h5><a href="blog-detail.php">Top trending and demands of online Liquor</a></h5>
                <span>22 June, 2023</span>
                <a href="blog-detail.php" class="text-link">read more
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path id="Vector" d="M5.02442 4.9663C5.02536 5.05124 5.01005 5.13552 4.97939 5.21432C4.94872 5.29313 4.90331 5.36489 4.84573 5.42551L1.11855 9.3181C1.00158 9.44027 0.842405 9.5094 0.676054 9.5103C0.509704 9.51119 0.349799 9.44377 0.231517 9.32288C0.113236 9.20198 0.0462661 9.0375 0.0453413 8.86563C0.0444166 8.69375 0.109612 8.52857 0.226586 8.4064L3.51897 4.9744L0.196093 1.57799C0.0930913 1.45508 0.0387609 1.29655 0.0439595 1.13408C0.049158 0.971613 0.113503 0.81718 0.224135 0.701639C0.334766 0.586097 0.483537 0.517959 0.640718 0.510839C0.797899 0.503719 0.951912 0.558141 1.07198 0.663232L4.84084 4.51549C4.95689 4.63508 5.02282 4.79699 5.02442 4.9663Z" fill="#2B2B2B"/>
                    </svg>
                </a>
            </div>
        </div>`

      for (let i = 0; i < productsPerPage; i++) {
          // Assuming each product has a unique identifier (e.g., product ID).
          const productId = (currentPage - 1) * productsPerPage + i + 1;
          fakeApiResponse.products.push({
              id: productId,
              name: `Product ${productId}`,
              data: `${blogBox}`
          });
      }

      // Append the new products to the product container with the 'hidden' class.
      const $productContainer = $('#blog-row');
      fakeApiResponse.products.forEach(product => {
          const productElement = $('<div class="col-md-4 col-sm-6 blog-col hidden"></div>');
          productElement.append(product.data);
          $productContainer.append(productElement);
      });

      currentPage++;
      isLoading = false;

      // Hide the loader text after products are loaded
        hideLoader();
  }

  // Initial load of products
  loadProducts();

  // Function to remove the 'hidden' class on scroll
  function revealProductsOnScroll() {
      const productElements = $('.blog-col.hidden');
      if (productElements.length > 0) {
          const windowHeight = $(window).height();
          const scrollTop = $(window).scrollTop();
          const bottomPosition = windowHeight + scrollTop;

          productElements.each(function () {
              const productTop = $(this).offset().top;
              if (productTop < bottomPosition) {
                  $(this).removeClass('hidden');
              }
          });
      }
  }

  // Load more products when the user scrolls to the bottom of the page.
  $(window).scroll(function () {
      if ($(window).scrollTop() + $(window).height() === $(document).height()) {
        loadProducts();        
      }
      
      // Call the function to reveal products on scroll
      revealProductsOnScroll();
  });
});


window.onload = function() {
  slideOne();
  slideTwo();
};

let sliderOne = document.getElementById("slider-1");
let sliderTwo = document.getElementById("slider-2");
let displayValOne = document.getElementById("range1");
let displayValTwo = document.getElementById("range2");
let minGap = 0;
let sliderTrack = document.querySelector(".slider-track");
let sliderMaxValue = document.getElementById("slider-1").max;

function slideOne() {
  if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
    sliderOne.value = parseInt(sliderTwo.value) - minGap;
  }
  displayValOne.textContent = sliderOne.value;
  fillColor();
}
function slideTwo() {
  if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
    sliderTwo.value = parseInt(sliderOne.value) + minGap;
  }
  displayValTwo.textContent = sliderTwo.value;
  fillColor();
}
function fillColor() {
  percent1 = (sliderOne.value / sliderMaxValue) * 100;
  percent2 = (sliderTwo.value / sliderMaxValue) * 100;
  sliderTrack.style.background = `linear-gradient(to right, #DDDDDD ${percent1}% , #FBB516 ${percent1}% , #FBB516 ${percent2}%, #DDDDDD ${percent2}%)`;
}