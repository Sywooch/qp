$(document).ready(function () {
    function getInterval() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        function toString(d, m, y) {
            if (d < 10) {
                d = '0' + d
            }

            if (m < 10) {
                m = '0' + m;
            }
            return d + '.' + m + '.' + y;
        }
        var end = toString(dd, mm, yyyy);

        if (mm - 1 < 0) {
            mm = 12;
            yyyy --;
        } else {
            mm--;
        }
        var start = toString(dd, mm, yyyy);
        return {
            start: start,
            end: end
        }
    }


    var interval = getInterval();
    $dateInterval = $('.date-interval');

    $dateInterval.daterangepicker({
        startDate: interval.start,
        endDate: interval.end,
        locale: {
            format: 'DD.MM.YYYY',
            applyLabel: "Принять",
            cancelLabel: "Отмена",
            daysOfWeek: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
        }
    });


    $start = $('.manager-date-start');
    $end = $('.manager-date-end');
    $('.daterangepicker .applyBtn').click(function () {
        var m = $dateInterval.val().split(" - ");
        var start = m[0].split(".").reverse().join("-");
        var end = m[0].split(".").reverse().join("-");
        // $dateInterval.val(start + ' - ' + end);
        console.log(start, end);
        $start.val(start);
        $end.val(end);
        $dateInterval.attr('name', '');
        $('.datepicker-form').submit();
    });

});