(function($, window, Drupal)
{
    Drupal.behaviors.admincontacts =
      {
        attach: function (context,settings) {
          var elements = context.getElementsByClassName("c-link")
          var setIframeSrc = function (e) {
            newsrc = this.getAttribute("href");
            document.getElementById("contacteditscreen").src = newsrc;
            e.preventDefault();
          };
          for (var i = 0; i < elements.length; i++)
          {
            elements[i].addEventListener('click', setIframeSrc, false);
          }
        }
      }
})(jQuery, window, Drupal);
