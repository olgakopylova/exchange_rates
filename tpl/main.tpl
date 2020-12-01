<!doctype html>
<html lang="ru">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="style/bootstrap-4.5.3/css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" href="style/css/main.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
        <!-- Material Design Bootstrap -->
        <link href="style/chart/scss/mdb.min.css" rel="stylesheet">
        <!-- Your custom styles (optional) -->
        <link href="style/charts/css/style.css" rel="stylesheet">
        <title>Курс валют</title>
    </head>
    <body>
    <script language="JavaScript" type="text/javascript" src="style/js/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="style/bootstrap-4.5.3/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="style/charts/js/mdb.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>
        <div class="wrapper">
            <div class="header">
                <?php echo $header;?>
            </div>
            <div class="jumbotron">
                <div class="container">
                    <div class="card" id="card-filter">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Фильтр</h4>
                                </div>
                            </div>
                        </div>
                        <form method="post" class="needs-validation" autocomplete="on">
                            <input id="type" name="type" value="filter" hidden>
                            <div class="form-group">
                                <label for="currency_type" class="control-label">Валюта</label>
                                <select name="currency_type" id="currency_type" class="form-control" >
                                    <option value="0">Все</option>
                                    <?php echo $select;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="user_patronymic">Дата начала периода</label>
                                        <input type="date" class="form-control" name="date_start" id="date_start">
                                    </div>
                                    <div class="col-6">
                                        <label for="user_patronymic">Дата окончания периода</label>
                                        <input type="date" class="form-control" name="date_end" id="date_end">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="show">Показать</button>
                            <a class="btn btn-primary" href="" onclick=" this.href='/exchange_rates/download.php?dateStart='+
                                   $('#date_start').val()+'&dateEnd='+$('#date_end').val()+'&type='+$('#currency_type').val();"
                                    traget="_blank">Сохранить json</a>
                        </form>
                    </div>
                    <div class="card" id="card-body">
                        <?php echo $body;?>
                    </div>
                    <div class="card">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <h4>График</h4>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info" role="alert">
                            График доступен для каждой валюты по отдельности. Пожалуйста, выберите в фильтре один тип валюты и период (необязательно).
                        </div>
                        <canvas id="lineChart"></canvas>
                    </div>
                    <div class="card" id="card-filter">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Ручная загрузка данных</h4>
                                </div>
                            </div>
                        </div>
                        <form method="post" class="needs-validation" autocomplete="on">
                            <input id="type" name="type" value="update" hidden>
                            <div class="form-group">
                                <label for="user_patronymic">Дата</label>
                                <input type="date" class="form-control" name="date" id="date" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="update">Загрузить</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer">
                <?php echo $footer;?>
            </div>
        </div>
    <script>
        tables_lang = {
            "decimal": ",",
            "thousands": " ",
            "lengthMenu": "Показывать _MENU_ записей",
            "zeroRecords": "Значение не найдено",
            "info": "Страница _PAGE_ из _PAGES_",
            "infoFiltered": "(найдено из _MAX_ записей)",
            "emptyTable": "Нет записей",
            "info": "Загружено _START_ - _END_ из _TOTAL_ записей",
            "infoEmpty": "Показано 0 из 0 записей",
            "infoPostFix": "",
            "loadingRecords": "Загрузка...",
            "processing": "Обработка...",
            "search": "Поиск:",
            "paginate": {
                "first": "Первая",
                "last": "Последняя",
                "next": "Следующая",
                "previous": "Предыдущая"
            },
            "aria": {
                "sortAscending": ": сортировать по возрастанию",
                "sortDescending": ": сортировать по убыванию"
            }
        };
        var params = {
            pagination: true,
            responsive: true,
            language: tables_lang,
            columnDefs : [{targets:3, type:"date-eu"}],
            sDom: '<"top"i>rt<"bottom"lp><"clear">',
            bInfo: false,
            bLengthChange: false
        };
        $(document).ready( function () {

            $('#currency').DataTable(params);
            $.fn.dataTable.moment('DD.MM.YYYY');
        } );

        $(document).on("submit", "form", function(event) {
            $('#send').attr('disabled', true);
            event.preventDefault();
            var d=new FormData(this);
            $.ajax({
                url: "index.php",
                type: "POST",
                dataType: "JSON",
                data: d,
                processData: false,
                contentType: false,
                success: function (data)
                {
                    if (!data['code']){
                        $('#card-body').html(data['content']);
                        $('#currency').DataTable(params);
                        var ctxL = document.getElementById("lineChart").getContext('2d');
                        var myLineChart = new Chart(ctxL, {
                            type: 'line',
                            data: data['chart'],
                            options: {
                                responsive: true
                            }
                        });
                        $('#send').attr('disabled', false);
                    }else{
                        alert(data['message']);
                    }
                },
            });

        });
    </script>
    </body>
</html>