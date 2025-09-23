// Barbershop theme JS (jQuery used for convenience)
$(function(){
  // Simulated booking submission
  $('#bookForm').on('submit', function(e){
    e.preventDefault();
    const $btn = $(this).find('button[type=submit]');
    $btn.prop('disabled', true).text('Requested');
    setTimeout(function(){
      $btn.prop('disabled', false).text('Request');
      // bootstrap toast
      const toastHtml = `<div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">Request received — we'll confirm shortly.</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>`;
      const $container = $('.toast-container');
      if (!$container.length) $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
      $('.toast-container').append(toastHtml);
      var t = new bootstrap.Toast($('.toast').last()[0]);
      t.show();
    }, 700);
  });

  // Smooth nav scrolling for internal anchors
  $('a.nav-link[href^="#"]').on('click', function(e){
    e.preventDefault();
    const target = $(this).attr('href');
    $('html,body').animate({scrollTop: $(target).offset().top - 20}, 400);
  });
  // Initialize AOS (animate on scroll)
  if (window.AOS) AOS.init({duration:700, once:true, easing:'ease-out-cubic'});

  // GSAP micro animation for logo on load
  if (window.gsap) {
    gsap.from('.logo', {y:-12, opacity:0, duration:0.8, ease:'power3.out'});
    gsap.from('.hero h2', {y:20, opacity:0, duration:0.9, delay:0.2, ease:'power3.out'});
  }
});
