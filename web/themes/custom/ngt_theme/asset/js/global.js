// // FUNCION GLOBAL DE EVENTO ANALYTICS
// function event_ga(action, category, label) {
//     gtag('event', action, {
//       'event_category': category,
//       'event_label': label
//     });
//   }
//   (function ($, Drupal) {
//     'use strict';
//     Drupal.behaviors.bootstrap_barrio_subtheme = {
//       attach: function (context, settings) {
  
//         $('body', context).once('inicio_ga_events').each(function () {
                  
//           // EVENTO AMIGOS
//           $('.friends').click(function () {
//             var id = $(this).data("id");
//             var friend = $('#node-' + id);
//             event_ga('clic', 'titulo_evento', 'mensaje');
//           });

//         });
//       }
//     };
//   })(jQuery, Drupal);
  