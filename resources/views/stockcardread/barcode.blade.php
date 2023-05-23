<link rel="stylesheet" href="{{ URL::asset('css/barcode/styles.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/barcode/example.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/barcode/pygment_trac.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<script src="{{ URL::asset('js/barcode/quagga.min.js') }}" type="text/javascript"></script>
<!-- <script src="{{ URL::asset('js/barcode/live_w_locator.js') }}" type="text/javascript"></script> -->
<script src="{{ URL::asset('js/barcode.js') }}" type="text/javascript"></script>
<script>
   
</script>
<section class="margin-barcode">
    <div id="kekka"></div>
    <section id="container" class="container">
        <div id="result_strip">
            <ul class="thumbnails"></ul>
        </div>
        <div id="interactive" class="viewport">
            <video id="cameraFeedContainer" autoplay="true" preload="auto" src="" muted="true" playsinline="true"></video>
            <canvas class="drawingBuffer" width="640" height="480">
            </canvas><br clear="all">
        </div>
    </section>
</section>