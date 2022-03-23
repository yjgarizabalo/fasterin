/*!
    * Start Bootstrap - Freelancer v6.0.5 (https://startbootstrap.com/theme/freelancer)
    * Copyright 2013-2020 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-freelancer/blob/master/LICENSE)
    */
(function($) {
    "use strict"; // Start of use strict
  
    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html, body').animate({
            scrollTop: (target.offset().top - 71)
          }, 1000, "easeInOutExpo");
          return false;
        }
      }
    });
  
    // Scroll to top button appear
    $(document).scroll(function() {
      var scrollDistance = $(this).scrollTop();
      if (scrollDistance > 100) {
        $('.scroll-to-top').fadeIn();
      } else {
        $('.scroll-to-top').fadeOut();
      }
    });
  
    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function() {
      $('.navbar-collapse').collapse('hide');
    });
  
    // Activate scrollspy to add active class to navbar items on scroll
    $('body').scrollspy({
      target: '#mainNav',
      offset: 80
    });
  
    // Collapse Navbar
    var navbarCollapse = function() {
      if ($("#mainNav").offset().top > 100) {
        $("#mainNav").addClass("navbar-shrink");
      } else {
        $("#mainNav").removeClass("navbar-shrink");
      }
    };
    // Collapse now if page is not at top
    navbarCollapse();
    // Collapse the navbar when page is scrolled
    $(window).scroll(navbarCollapse);
  
    // Floating label headings for the contact form
    $(function() {
      $("body").on("input propertychange", ".floating-label-form-group", function(e) {
        $(this).toggleClass("floating-label-form-group-with-value", !!$(e.target).val());
      }).on("focus", ".floating-label-form-group", function() {
        $(this).addClass("floating-label-form-group-with-focus");
      }).on("blur", ".floating-label-form-group", function() {
        $(this).removeClass("floating-label-form-group-with-focus");
      });
    });

    var autocollapse = function(menu, maxHeight) {
      var nav = $(menu);
      var navHeight = nav.innerHeight();
      if (navHeight >= maxHeight) {
        $(menu + ' .dropdown').removeClass('d-none');
        $(".navbar-nav").removeClass('w-auto').addClass("w-100");
        while (navHeight > maxHeight) {
          //  add child to dropdown
          var children = nav.children(menu + ' li:not(:last-child)');
          var count = children.length;
          $(children[count - 1]).prependTo(menu + ' .dropdown-menu');
          navHeight = nav.innerHeight();
        }
        $(".navbar-nav").addClass("w-auto").removeClass('w-100');
      } else {
        var collapsed = $(menu + ' .dropdown-menu').children(menu + ' li');
        if (collapsed.length === 0) {
          $(menu + ' .dropdown').addClass('d-none');
        }
        while (navHeight < maxHeight && (nav.children(menu + ' li').length > 0) && collapsed.length > 0) {
          //  remove child from dropdown
          collapsed = $(menu + ' .dropdown-menu').children('li');
          $(collapsed[0]).insertBefore(nav.children(menu + ' li:last-child'));
          navHeight = nav.innerHeight();
        }
        if (navHeight > maxHeight) {
          autocollapse(menu, maxHeight);
        }
      }
    }

    autocollapse('#nav', 60);

    // function to not resize on small and extra-small screen sizes
    var configNav = function() {
      if ($(window).width() >= 974 ) autocollapse('#nav', 60);
      else {
        let collapsed = $('#nav .dropdown-menu').children('li');
        $('#nav .dropdown').addClass('d-none');
        if (collapsed.length > 0){
          for (let i = 0; i < collapsed.length; i++) {
            $(collapsed[i]).insertBefore($('#nav').children('#nav li:last-child'));
          }

        }
      }
    }

    // when the window is resized
    $(window).on('resize', function() {
      configNav();
    });

    // live search
    $('.live-search-box').on('keyup', function () {
      var text_search = $(this).val().toLowerCase();
      $(".live-search-table tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(text_search) > -1)
      });
    });

  })(jQuery); // End of use strict