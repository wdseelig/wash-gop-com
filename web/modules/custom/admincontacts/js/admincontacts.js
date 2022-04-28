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
            $(".clrscreen").css("visibility", 'visible');
          };
          for (var i = 0; i < elements.length; i++)
          {
            elements[i].addEventListener('click', setIframeSrc, false);
          }
          var goback = context.getElementsByClassName("clrscreen")
          var setClrScreen = function (c) {
           location.href="/admincontacts";
            c.preventDefault();
          };
          for (var i = 0; i < goback.length; i++)
          {
            goback[i].addEventListener('click', setClrScreen, false);
          }
        }

      }
})(jQuery, window, Drupal);
