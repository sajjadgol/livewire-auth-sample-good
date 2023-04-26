<!DOCTYPE html>
<html style="height:100%"  lang='en' dir="{{ Route::currentRouteName() == 'rtl' ? 'rtl' : '' }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  @if(config('app_settings.app_favicon_logo.value'))
    <link rel="apple-touch-icon" sizes="76x76" href="{{  Storage::disk(config('app_settings.filesystem_disk.value'))->url(config('app_settings.app_favicon_logo.value')) }}">
  @else
   <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
  @endif

  @if(config('app_settings.app_favicon_logo.value'))
    <link rel="icon" type="image/png" href="{{  Storage::disk(config('app_settings.filesystem_disk.value'))->url(config('app_settings.app_favicon_logo.value')) }}">
  @else
    <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
  @endif
  <title>
  {{ config('app_settings.app_name.value') ?? config('app.name')}} | Food Delivery, Dining and Store Discovery Service. Better food for more
  </title>
  
  <link  href="{{ mix('css/app.css') }}" rel="stylesheet">
 
 @livewireStyles 

  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NTWTLTM');</script>
  <!-- End Google Tag Manager -->


</head>
<body style="height:100%" class="g-sidenav-show bg-gray-200 {{ Route::currentRouteName() == 'rtl' ? 'rtl' : '' }}">
    {{ $slot }}
  <script src="{{ mix('js/app.js') }}"></script>
  <script>
    
    // Alert Modal windows
    window.addEventListener('alert', event => { 
        toastr[event.detail.type](event.detail.message, 
        event.detail.title ?? ''), toastr.options = {
              "closeButton": true,
              "progressBar": true,
          }
      });
  
      // Slider bar animations
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script> 

  <!-- Google Maps kyes  -->
  <script type="text/javascript"
              src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places"></script>
  <!-- Google Map load -->

  <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NTWTLTM"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <script>
      google.maps.event.addDomListener(window, 'load', initialize);
      function initialize() {
          var input = document.getElementById('googleMapAutocomplete');
          if(input){
              var autocomplete = new google.maps.places.Autocomplete(input);  
              autocomplete.addListener('place_changed', function () {
                  var place = autocomplete.getPlace();             
                  $('#latitude').val(place.geometry['location'].lat());              
                  $('#longitude').val(place.geometry['location'].lng());
                  console.log(place);  
                  window.livewire.emit('set:latitude-longitude',place.geometry['location'].lat(), place.geometry['location'].lng(), place.formatted_address) ;
              });
          }
      }            
  </script>
  
  @stack('js')
  @livewireScripts 
 
</body>
</html>
