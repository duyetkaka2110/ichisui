@extends("layouts.layout")
@section("js")
<script src='/js/lib/mainday.js'></script>
<script src='/js/lib/locales/ja.js'></script>
<script src="/js/popper.min.js"></script>
<script src="/js/tooltip.min.js"></script>
<script src="/js/moment.min.js"></script>
<script>
    resourcesData = <?php echo $resourcesData ?>;
    selectDate = '<?php echo $selectDate ?>';
</script>
<script src="/js/day.js"></script>
@endsection
@section("content")
<main class="position-relative p-3">
    @include("schedule.head-menu")
    <div class="main-content">
        <div id='calendar'>
        </div>
    </div>
</main>
<style>
    .fc-h-event .fc-event-main {
        color: #000;
        color: var(--fc-event-text-color, #000);
    }

    .fc-h-event .fc-event-main-frame {
        display: block;
    }

    .fc-next-button,
    .fc-prev-button,
    .fc-OneWeekNextButton-button,
    .fc-OneWeekPrevButton-button  {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .fc-today-button {
        margin-right: 10px !important;
    }
</style>
@endsection