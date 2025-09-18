// Basic mega menu interactions for mobile
document.addEventListener('DOMContentLoaded', function(){
  var t = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.primary-nav');
  if (t && nav) {
    t.addEventListener('click', function(){
      nav.classList.toggle('is-open');
    });
  }
  // Mobile dropdown toggles
  document.querySelectorAll('.primary-nav .menu-item-has-children > a').forEach(function(a){
    a.addEventListener('click', function(e){
      if (window.innerWidth <= 900){
        e.preventDefault();
        var li = a.parentElement;
        li.classList.toggle('open');
      }
    });
  });
});
