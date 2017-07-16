$(document).ready(function () {

    Date.prototype.today = function () {
        return ((this.getDate() < 10)?"0":"") + this.getDate() +"-"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"-"+ this.getFullYear();
    };
    Date.prototype.timeNow = function () {
        return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
    };
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

    function getFilter() {

        var m = $dateInterval.val().split(" - ");
        console.log($dateInterval.val());

        return {
            start: m[0].split(".").reverse().join("-"),
            end: m[1].split(".").reverse().join("-")
        }
    }
    $('.daterangepicker .applyBtn').click(function () {
        setTimeout(function () {
            var f = getFilter();
            // $dateInterval.val(start + ' - ' + end);
            $start.val(f.start);
            $end.val(f.end);
            $dateInterval.attr('name', '');
            $('.datepicker-form').submit();
        }, 0);
    });
    function loadFile(url,callback){
        JSZipUtils.getBinaryContent(url,callback);
    }

    function getFile(url, tmplPath, nameFile) {
        var dateTime = new Date().today() + "_" + new Date().timeNow();
        setTimeout(function () {
            var filter = getFilter();
            $.ajax( {
                url: url,//after=" + filter.start +"&before=" + filter.end,
                dataType: "json",
                type: "get",
                success: function( data ) {
                    loadFile(tmplPath,function(error,content){
                        if (error) { throw error };
                        var zip = new JSZip(content);
                        var doc = new Docxtemplater().loadZip(zip);
                        doc.setData({
                            date_generated: dateTime,
                            order: data
                        });
                        try {
                            // render the document (replace all occurrences of {first_name} by John, {last_name} by Doe, ...)
                            doc.render()
                        }
                        catch (error) {
                            var e = {
                                message: error.message,
                                name: error.name,
                                stack: error.stack,
                                properties: error.properties
                            };
                            console.log(JSON.stringify({error: e}));
                            // The error thrown here contains additional information when logged with JSON.stringify (it contains a property object).
                            throw error;
                        }
                        var out = doc.getZip().generate({
                            type:"blob",
                            mimeType: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                        }) //Output the document using Data-URI
                        saveAs(out,  nameFile + dateTime  + ".docx");
                    });
                },
                error: function () {
                    App.log('Error #11');
                    App.message('Произошла ошибка. Списоке заказов не удалось получить', false);
                }
            } );
        }, 0);
    }

    $('.js-print').click(function () {
        getFile("/manager/get-orders-json?", "../docs/list.tmpl.docx", "orders_");
    });

    $('.js-print-order').click(function () {
        var id = $(this).data('order-id');
        getFile("/manager/get-order-content-json?id=" + id, "../docs/order.tmpl.docx", "descr_");
    });
});
