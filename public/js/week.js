$(document).ready(function() {
    $('.main-content').css('height', $(window).height() - $("header").height() - $(".main-header").height() - 70);
})
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: '0421447451-fcs-1639372307',
        scrollTime: '08:00', // undo default 6am scrollTime
        locale: 'ja',
        firstDay: 1,
        height: "100%",
        initialDate: selectDate,
        selectable: true,
        headerToolbar: {
            left: 'today,prev,title,next',
            center: '',
            right: ''
        },
        initialView: 'timeGridWeek',
        navLinks: true, // can click day/week names to navigate views
        editable: false,
        buttonText: {
            today: '今週',
        },
        views: {
            timeGridWeek: {
                titleFormat: function(date) {
                    const startMonth = date.start.month + 1;
                    const endMonth = date.end.month + 1;
                    return date.start.year + '年' + startMonth + '月' + date.start.day + '日～' + date.end.year + '年' + endMonth + '月' + date.end.day + '日';
                },
            },
        },
        resourceAreaHeaderContent: 'ユーザ',
        resources: [{
            "id": "0000000001",
            "title": "\u30b8\u30a7\u30c3\u30c8\r\n"
        }, {
            "id": "0000000002",
            "title": "\u677e\u7530\r\n"
        }],
        dayMaxEvents: true, // allow "more" link when too many events
        events: function(info, successCallback, failureCallback) {
            new_url = '/getdata?day=&start=' + info.start.valueOf() + "&end=" + info.end.valueOf() + "&UserID=" + $('input[name=UserID]:checked').val();
            window.history.replaceState(null, null, "?date="+info.start.valueOf());
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

        dayCellDidMount: function(arg) {
            checkdate = arg.date.getFullYear() + "-" + (arg.date.getMonth() + 1) + "-" + arg.date.getDate()
            $.getJSON('https://www.googleapis.com/calendar/v3/calendars/vi.japanese%23holiday%40group.v.calendar.google.com/events?key=AIzaSyDNg5-iEZkvfQVjSUitDXz4k68lCRZ5nao&timeMin=' + checkdate + 'T13%3A00%3A00%2B09%3A00&timeMax=' + checkdate + 'T23%3A59%3A59%2B09%3A00&singleEvents=false&maxResults=9999' + '&callback=?', function(data) {
                // JSON result in `data` variable
                if (data["items"].length) {
                    arg.el.classList.add("fc-holi")
                    arg.el.classList.add("fc-day-sun");
                    $("." + arg.el.classList[2]).addClass("fc-day-sun")
                }
            });
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