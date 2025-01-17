let userBox = document.querySelector('.header .header-2 .user-box');

document.querySelector('#user-btn').onclick = () =>{
   userBox.classList.toggle('active');
   navbar.classList.remove('active');
}

let navbar = document.querySelector('.header .header-2 .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   userBox.classList.remove('active');
}

window.onscroll = () =>{
   userBox.classList.remove('active');
   navbar.classList.remove('active');

   if(window.scrollY > 60){
      document.querySelector('.header .header-2').classList.add('active');
   }else{
      document.querySelector('.header .header-2').classList.remove('active');
   }
}

/**********ADD TO CART MODAL**********/


/**************FORDA ZOOM2 AND ADD TO CART MODAL************/
$(document).ready(function() {
   $('.image-container').hover(function() {
      $(this).find('.image').css('transform', 'scale(1.5)'); // Increase scale on hover
   }, function() {
      $(this).find('.image').css('transform', 'scale(1)'); // Reset scale on hover out
   });

   // Get the modal element
   var modal = document.getElementById('myModal');

   // Get the <span> element that closes the modal
   var span = document.getElementsByClassName("close")[0];

   // Close the modal when the user clicks on <span> (x)
   span.onclick = function() {
      modal.style.display = "none";
   }

   // Close the modal when the user clicks anywhere outside of it
   window.onclick = function(event) {
      if (event.target == modal) {
         modal.style.display = "none";
      }
   }
});
