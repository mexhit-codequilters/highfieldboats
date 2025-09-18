document.addEventListener('DOMContentLoaded', function(){
  var t = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.primary-nav');
  if (t && nav) {
    t.addEventListener('click', function(){
      nav.classList.toggle('is-open');
    });
  }
});
