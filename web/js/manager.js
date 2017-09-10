$(document).ready(function () {

    Date.prototype.today = function () {
        return ((this.getDate() < 10)?"0":"") + this.getDate() +"-"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"-"+ this.getFullYear();
    };
    Date.prototype.timeNow = function () {
        return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
    };

    function loadFile(url,callback){
        JSZipUtils.getBinaryContent(url,callback);
    }

    $start = $('.form-control[name="after"]');
    $end = $('.form-control[name="before"]');

    function getFile(url, tmplPath, nameFile, isSingle) {
        var dateTime = new Date().today() + "_" + new Date().timeNow();
        setTimeout(function () {
            $.ajax( {
                url: url + "after=" + $start.val() +"&before=" + $end.val(),
                dataType: "json",
                type: "get",
                success: function( data ) {
                    var _data = {};
                    if (isSingle) {
                        if( 'order' in data ) {
                            var order = data.order;
                            _data = {
                                date_generated: dateTime,
                                total_price: data.total_price,
                                total_products: data.total_products,
                                created: order.created,
                                email: order.email,
                                id_order: order.id_order,
                                price: order.price,
                                status: order.status,
                                products: data.products
                            }
                        } else {
                            console.log(data);
                            return;
                        }
                    } else {
                        _data = {
                            date_generated: dateTime,
                            order: data
                        }
                    }
                    console.log(_data);
                    loadFile(tmplPath,function(error,content){
                        if (error) { throw error; }
                        var zip = new JSZip(content);
                        var doc = new Docxtemplater().loadZip(zip);
                        doc.setData(_data);
                        try {
                            // render the document (replace all occurrences of {first_name} by John, {last_name} by Doe, ...)
                            doc.render();
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
        getFile("/manager/get-orders-json?", "../docs/list.tmpl.docx", "orders_", false);
    });

    $('.js-print-order').click(function () {
        var id = $(this).data('order-id');
        getFile("/manager/get-order-content-json?id=" + id, "../docs/order.tmpl.docx", "descr_", true);
    });

    $('*[data-route]').on("click", function () {
        var route = $(this).data('route');
        window.location = route;
    });

    $('.js-clear').on("click", function () {
        console.log("fdf");
        if ($(this).hasClass('before')) {
            $start.val("");
        } else {
            $end.val("");
        }
    });

    $('[data-toggle="tooltip"]').tooltip();
});
