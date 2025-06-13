/* ===============================================
  TABS
=============================================== */
jQuery(document).ready(function () {
  jQuery( ".tablinks" ).first().addClass( "active" );
  jQuery( ".tabcontent" ).first().addClass( "active" );
});

function bridal_dresses_services_tab(evt, cityName) {
  var bridal_dresses_i, bridal_dresses_tabcontent, bridal_dresses_tablinks;
  bridal_dresses_tabcontent = document.getElementsByClassName("tabcontent");
  for (bridal_dresses_i = 0; bridal_dresses_i < bridal_dresses_tabcontent.length; bridal_dresses_i++) {
    bridal_dresses_tabcontent[bridal_dresses_i].style.display = "none";
  }
  bridal_dresses_tablinks = document.getElementsByClassName("tablinks");
  for (bridal_dresses_i = 0; bridal_dresses_i < bridal_dresses_tablinks.length; bridal_dresses_i++) {
    bridal_dresses_tablinks[bridal_dresses_i].className = bridal_dresses_tablinks[bridal_dresses_i].className.replace(" active", "");
  }
  jQuery('#'+ cityName).show()
  evt.currentTarget.className += " active";

}

bridal_dresses_initializeOwlCarousel();

function bridal_dresses_initializeOwlCarousel() {
  jQuery('.services_main_box .owl-carousel').each(function () {
    var owl = jQuery(this);
    // Destroy the carousel if already initialized to prevent multiple instances
    if (owl.hasClass('owl-loaded')) {
      owl.trigger('destroy.owl.carousel'); // Destroys previous initialization
    }

    // Initialize the carousel
    owl.owlCarousel({
      margin: 50,
      stagePadding: 100,
      nav: true,
      autoplay: true,
      lazyLoad: true,
      rtl:false,
      autoplayTimeout: 5000,
      loop: true,
      dots: false,
      navText: ['<i class="far fa-arrow-alt-circle-left"></i>', '<i class="far fa-arrow-alt-circle-right"></i>'],
      responsive: {
        0: {
        items: 1,
        margin: 10,
        stagePadding: 10,
          },
          576: {
            items: 1
          },
          768: {
            margin: 30,
            items: 2
          },
          1000: {
            items: 3
          },
          1200: {
            items: 4
          }
      },
      autoplayHoverPause: false,
      mouseDrag: true
    });
  });
}

jQuery(function($) {

    /* -----------------------------------------
    Preloader
    ----------------------------------------- */
    $('#preloader').delay(1000).fadeOut();
    $('#loader').delay(1000).fadeOut("slow");

    /* -----------------------------------------
    Navigation
    ----------------------------------------- */
    $('.menu-toggle').click(function() {
        $(this).toggleClass('open');
    });

    /* -----------------------------------------
    Keyboard Navigation
    ----------------------------------------- */
    $(window).on('load resize', bridal_dresses_navigation)

    function bridal_dresses_navigation(event) {
        if ($(window).width() < 1200) {
            $('.main-navigation').find("li").last().bind('keydown', function(e) {
                if (e.shiftKey && e.which === 9) {
                    if ($(this).hasClass('focus')) {
                    }

                } else if (e.which === 9) {
                    e.preventDefault();
                    $('#masthead').find('.menu-toggle').focus();
                }                
            })
        } else {
            $('.main-navigation').find("li").unbind('keydown')
        }
    }

    bridal_dresses_navigation()

    var bridal_dresses_primary_menu_toggle = $('#masthead .menu-toggle');
    bridal_dresses_primary_menu_toggle.on('keydown', function(e) {
        var tabKey = e.keyCode === 9;
        var shiftKey = e.shiftKey;

        if (bridal_dresses_primary_menu_toggle.hasClass('open')) {
            if (shiftKey && tabKey) {
                e.preventDefault();
                const $the_last_li = $('.main-navigation').find("li").last()
                $the_last_li.find('a').focus()
                if (!$the_last_li.hasClass('focus')) {

                    const $is_parent_on_top = true
                    let $the_parent_ul = $the_last_li.closest('ul.sub-menu')

                    let count = 0

                    while (!!$the_parent_ul.length) {
                        ++count

                        const $the_parent_li = $the_parent_ul.closest('li')

                        if (!!$the_parent_li.length) {
                            $the_parent_li.addClass('focus')
                            $the_parent_ul = $the_parent_li.closest('ul.sub-menu')

                            // Blur the cross
                            $(this).blur()
                            $the_last_li.addClass('focus')
                        }

                        if (!$the_parent_ul.length) {
                            break;
                        }
                    }

                }

            };
        }
    })

    /* -----------------------------------------
    Main Slider
    ----------------------------------------- */

    // Determine if the document is RTL
    var isRtl = $('html').attr('dir') === 'rtl';
    
    $('.banner-slider').slick({
        autoplaySpeed: 3000,
        dots: false,
        arrows: true,
        nextArrow: '<button class="fas fa-angle-right slick-next"></button>',
        prevArrow: '<button class="fas fa-angle-left slick-prev"></button>',
        rtl: isRtl, // Set the rtl option
        responsive: [{
            
            breakpoint: 1025,
            settings: {
                dots: true,
                arrows: false,
            }
        },
        {
            breakpoint: 480,
            settings: {
                dots: true,
                arrows: false,
            }
        }]
    });

    /* -----------------------------------------
    Scroll Top
    ----------------------------------------- */
    var bridal_dresses_scrollToTopBtn = $('.bridal-dresses-scroll-to-top');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 400) {
            bridal_dresses_scrollToTopBtn.addClass('show');
        } else {
            bridal_dresses_scrollToTopBtn.removeClass('show');
        }
    });

    bridal_dresses_scrollToTopBtn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, '300');
    });

    //search js

    $(".input").focus(function() {
      $(".form").addClass("move");
    });
    $(".input").focusout(function() {
      $(".form").removeClass("move");
      $(".input").val("");
    });

    $(".fa-search").click(function() {
      $(".input").toggleClass("active");
      $(".form").toggleClass("active");
    });
    
});

document.addEventListener('DOMContentLoaded', function() {
  const header = document.querySelector('.sticky-header');
  if (header) { // Check if header exists
    window.addEventListener('scroll', function() {
      if (window.scrollY > 0) {
        header.classList.add('is-sticky');
      } else {
        header.classList.remove('is-sticky');
      }
    });
  }
});

jQuery(".banner-section.banner-style-1 .banner-single .banner-caption .banner-catption-wrapper .banner-caption-title a").html(function() {
    var textArray = jQuery(this).text().trim().split(" ");
    
    if (textArray.length > 3) {
        // Extract the last three words
        var lastThreeWords = textArray.splice(-3, 3).join(" ");
        // Join the remaining text and add the last three words wrapped in a span
        var updatedText = textArray.join(" ") + "<br><span class='banner-text'>" + lastThreeWords + "</span>";
        return updatedText;
    } else {
        // If there are three or fewer words, wrap all of them in a span
        return "<span class='banner-text'>" + textArray.join(" ") + "</span>";
    }
});