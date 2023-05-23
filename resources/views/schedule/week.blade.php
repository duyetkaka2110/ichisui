@extends("layouts.layout")

@section("js")
<script src='/js/lib/main.js'></script>
<script src='/js/lib/locales/ja.js'></script>
<script src="/js/popper.min.js"></script>
<script src="/js/tooltip.min.js"></script>
<script src="/js/moment.min.js"></script>

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
    .fc-icon-chevron-left:before {
        content: "\e902";
    }

    .fc-icon-chevron-right:before {
        content: "\e903";
    }

    .fc .fc-today-button {
        color: #fff;
        color: var(--fc-button-text-color, #fff);
        background-color: #1e2b37;
        background-color: var(--fc-button-hover-bg-color, #1e2b37);
        border-color: #1a252f;
        border-color: var(--fc-button-hover-border-color, #1a252f);
    }
</style>
<script>
    selectDate = '<?php echo $selectDate ?>';
</script>
<script src="/js/week.js"></script>
@endsection