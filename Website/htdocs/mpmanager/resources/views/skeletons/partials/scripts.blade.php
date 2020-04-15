<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<script type="text/javascript" data-pace-options='{ "restartOnRequestAfter": true }'
        src="{{asset('admin/js/plugin/pace/pace.min.js')}}"></script>

<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->

<script>
  if (!window.jQuery) {
    document.write('<script src="{{asset('admin/js/libs/jquery-3.2.1.min.js')}}"><\/script>')
  }
</script>


<script>
  if (!window.jQuery.ui) {
    document.write('<script src="{{asset('admin/js/libs/jquery-ui.min.js')}}"><\/script>')
  }
</script>

<!-- IMPORTANT: APP CONFIG -->
<script type="text/javascript" src="{{asset('admin/js/app.config.js')}}"></script>

<!-- IMPORTANT: APP CONFIG -->
<script type="text/javascript" src="{{asset('admin/js/app.js')}}"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
<script type="text/javascript" src="{{asset('admin/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js')}}"></script>

<!-- BOOTSTRAP JS -->
<script type="text/javascript" src="{{asset('admin/js/bootstrap/bootstrap.min.js')}}"></script>

<!-- CUSTOM NOTIFICATION -->
<script type="text/javascript" src="{{asset('admin/js/notification/SmartNotification.min.js')}}"></script>

<!-- JARVIS WIDGETS -->
<script type="text/javascript" src="{{asset('admin/js/smartwidgets/jarvis.widget.min.js')}}"></script>


<!-- SPARKLINES -->
<script type="text/javascript" src="{{asset('admin/js/plugin/sparkline/jquery.sparkline.min.js')}}"></script>


<!-- browser msie issue fix -->
<script type="text/javascript" src="{{asset('admin/js/plugin/msie-fix/jquery.mb.browser.min.js')}}"></script>

@yield('footer-scripts')

<script>
  // DO NOT REMOVE : GLOBAL FUNCTIONS!
  $(document).ready(function () {
    pageSetUp()
      @yield('documentReady')


  })


</script>
