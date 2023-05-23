@section('title', '勤怠スケジュール')
@section("css")
<link href='/js/lib/main.css' rel='stylesheet' />
<link href='/css/day.css' rel='stylesheet' />
@endsection
 <div class="main-header pt-3">
    <div class="m-title">
        <h4 class="pb-2">勤怠スケジュール</h4>
    </div>
</div>
<div class="position-absolute mg-btnsubmit">
    <div>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-light p-0 txtUserID WorkStatus0 w-8em">
                <input type="radio" class="UserID" name="UserID" value="{{ @Auth::user()->UserID }}" autocomplete="off"> {{ @Auth::user()->UserNM }}
            </label>
            <label class="btn btn-light p-0 txtUserID WorkStatus1 active w-8em">
                <input type="radio" checked="" name="UserID" class="UserID" value="" autocomplete="off"> 全体
            </label>
        </div>
    </div>
    <div class="mt-2">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-light p-0 WorkStatus0 btnSchedule {{ Helper::activeMenu('b.schedule.week') }} w-8em" href="{{ route('b.schedule.week') }}">
                <input type="radio" {{ Helper::activeMenu('b.schedule.week') == 'active' ? "checked": '' }}  name="btnSchedule" value="週次" autocomplete="off"> 週次
            </label>
            <label class="btn btn-light p-0 WorkStatus1 btnSchedule {{ Helper::activeMenu('b.schedule.day') }} w-8em" href="{{ route('b.schedule.day') }}">
                <input type="radio" name="btnSchedule" {{ Helper::activeMenu('b.schedule.day') == 'active' ? "checked": '' }}  value="日次" autocomplete="off"> 日次
            </label>
        </div>
    </div>
</div>