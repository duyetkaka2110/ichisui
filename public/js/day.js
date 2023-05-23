$(document).ready(function() {
    $('.main-content').css('height', $(window).height() - $("header").height() - $(".main-header").height() - 70);
})
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

console.info(selectDate)
    var calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: '0421447451-fcs-1639372307',
        scrollTime: '08:00', // undo default 6am scrollTime
        locale: 'ja',
        firstDay: 1,
        initialDate: selectDate,
        height: "100%",
        selectable: true,
        customButtons: {
            OneWeekNextButton: {
                icon: "chevrons-right",
                click: function() {
                    var current = calendar.getDate();
                    new_date = moment(moment(current).add(7, 'd').format('YYYY-MM-DD'))
                    calendar.gotoDate(new_date.format('YYYY-MM-DD'));
                }
            },
            OneWeekPrevButton: {
                icon: "chevrons-left",
                click: function() {
                    var current = calendar.getDate();
                    new_date = moment(moment(current).add(-7, 'd').format('YYYY-MM-DD'))
                    calendar.gotoDate(new_date.format('YYYY-MM-DD'));
                }
            },
        },
        buttonText: {
            today: '今日',
        },
        headerToolbar: {
            left: 'today,OneWeekPrevButton,prev,title,next,OneWeekNextButton',
            center: '',
            right: '',
        },
        initialView: 'resourceTimelineDay',
        navLinks: true, // can click day/week names to navigate views
        editable: false,
        resourceAreaHeaderContent: 'ユーザ',
        resourceOrder: 'sort',
        resources: resourcesData,
        dayMaxEvents: true, // allow "more" link when too many events
        events: function(info, successCallback, failureCallback) {
            new_url = '/getdata?day=true&start=' + info.start.valueOf() + "&end=" + info.end.valueOf() + "&UserID=" + $('input[name=UserID]:checked').val();
            window.history.replaceState(null, null, "?date="+info.end.valueOf());
            $.ajax({
                url: new_url,
                type: 'get',
                success: function(response) {
                    successCallback(response);
                },
                beforeSend: function() {
                    $('.loading').removeClass('d-none');
                },
                complete: function() {
                    $('.loading').addClass('d-none');
                },
            })
        },
        eventDidMount: function(info) {
            if (!info.isMirror) {
                var tooltip = new Tooltip(info.el, {
                    title: "<label>" + info.event.extendedProps.WWID + "</label><div class='tttt'>" + info.event.extendedProps.category + '<br>' + dateformat(info.event.start) + "-" + dateformat(info.event.end) + "<br>" + info.event.extendedProps.userNM + "</div>",
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body',
                    html: true,
                    isMirror: false
                });
            }
        },
    });

    calendar.render();
    btnSchedule(calendar);
});

function btnSchedule(calendar) {

    UserIDchecked = ''
    $(".UserID").on("click", function() {
        if ($(this).val() != UserIDchecked) {
            calendar.refetchEvents()
            UserIDchecked = $(this).val()
        }
    })
    $(".btnSchedule").on("click", function() {
        $('.loading').removeClass('d-none');
        window.location.href = $(this).attr("href");
    })
}

function dateformat(dateT) {
    var date = moment(dateT);
    return date.format("HH:mm");
}