<?php
include('./functions.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grafik</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <?php

    function replace_month($month)
    {
        $short_month_name = substr($month,3,3);
        switch ($short_month_name)
        {
            case 'Jan':
                $short_month_name = 'sty';
                break;
            case 'Feb':
                $short_month_name = 'lut';
                break;
            case 'Mar':
                $short_month_name = 'mar';
                break;
            case 'Apr':
                $short_month_name = 'kwi';
                break;
            case 'May':
                $short_month_name = 'maj';
                break;
            case 'Jun':
                $short_month_name = 'cze';
                break;
            case 'Jul':
                $short_month_name = 'lip';
                break;
            case 'Aug':
                $short_month_name = 'sie';
                break;
            case 'Sep':
                $short_month_name = 'wrz';
                break;
            case 'Oct':
                $short_month_name = 'paź';
                break;
            case 'Nov':
                $short_month_name = 'lis';
                break;
            case 'Dec':
                $short_month_name = 'gru';
                break;
        }
        $month = substr_replace($month, $short_month_name, 3, 3);
        return $month;
    }

    $colors = ['255,255,255','255,226,204','204,238,255','10,100,255'];
    $colors_classes = ['schedule-bg-white','schedule-bg-orange','schedule-bg-lightblue','schedule-bg-blue'];
    $days_initials = ['P','W','Ś','C','P','S','N'];

    ?>

    <div class="report-main grafik-main">

        <table class="grafik-table">
        <?php 

            $diff = (date("N")-1)+7;
            $first_day_of_this_week = date('Y-m-d',strtotime("-$diff days"));
            
            $days_array = [];

            for ($i=0; $i<8; ++$i)
            {
                $counter = $i * 7;
                for ($j=0; $j<7; ++$j)
                {
                    array_push($days_array,date("y_m_d",strtotime("+".($counter+$j)." days", strtotime($first_day_of_this_week))));
                }
            }

            // FIRST ROW TABLE

            echo "<tr>";

            // TOP LEFT SQUARE
            echo "<td rowspan='3' class='text-center' style='background-color:rgb(".$colors[1].")'>Projekty</td>";

            $week_of_the_year = new DateTime($first_day_of_this_week);
            $week_of_the_year = $week_of_the_year->format('W');
            $date_to_print = '';

            for ($i=0; $i<8; ++$i)
            {
                $date_to_print = date("d M Y",strtotime("+".($i*7)." days",strtotime($first_day_of_this_week)));
                $date_to_print = replace_month($date_to_print);
                echo "<td class='text-center' colspan='7' style='background-color:rgb(".$colors[$i%2].")'>Tydzień ".(intval($week_of_the_year)+$i)."<br>".$date_to_print."</td>";
            }

            // TOP RIGHT SQUARE
            //echo "<td rowspan='3' class='text-center' style='background-color:rgb(".$colors[0].")'>Dezaktywuj</td>";

            echo "</tr>";

            // SECOND ROW TABLE
            $handler = 0;
            $counter = 0;
            $counter_today = 0;

            echo "<tr>";
            foreach ($days_array as $day)
            {
                if ($counter == 7) 
                {
                    $counter = 0;
                    $handler++;
                }
                if ($day == date("y_m_d"))
                {
                    echo "<td class='grafik-table-day text-center ".$colors_classes[2]."'>".(intval(substr($day,strlen($day)-2,2)))."</td>";
                    $counter_today = $handler * 7 + $counter;
                }
                else 
                {
                    echo "<td class='grafik-table-day text-center ".$colors_classes[$handler%2]."'>".(intval(substr($day,strlen($day)-2,2)))."</td>";
                }
                $counter++;
            }
            echo "</tr>";

            // THIRD ROW TABLE

            echo "<tr>";
            for ($i=0; $i<8; ++$i)
            {
                for ($j=0; $j<7; ++$j)
                {
                    if ($counter_today == ($i*7+$j)) 
                    {
                        echo "<td class='grafik-table-day text-center' style='background-color:rgb(".$colors[2].")'>".$days_initials[$j]."</td>";
                    }
                    else
                    {
                        echo "<td class='grafik-table-day text-center' style='background-color:rgb(".$colors[$i%2].")'>".$days_initials[$j]."</td>";
                    }
                }
            }
            echo "</tr>";

            // PRINT ALL PROJECTS

            $sql = "SELECT id, full_name FROM projects";
            $res = $schedule->query($sql);

            $found_days_array = [];
            $handler = 0;
            $counter = 0;
            $bg_color = '';

            while ($row = $res->fetch_assoc())
            {
                echo "<tr><td style='background-color:rgb(".$colors[1].")'>".$row['full_name']."</td>";

                $sql = "SELECT * FROM `".$row['id']."` WHERE day >= '".$days_array[0]."' AND day <= '".$days_array[55]."'";
                $result = $schedule->query($sql);
                while ($found_day = $result->fetch_assoc())
                {
                    array_push($found_days_array,$found_day['day']);
                }

                foreach ($days_array as $day)
                {
                    if ($counter == 7)
                    {
                        $counter = 0;
                        $handler++;
                    }
                    ($day == date("y_m_d")) ? $bg_color = $colors_classes[2] : $bg_color = $colors_classes[$handler%2];
                    if (in_array($day,$found_days_array)) 
                    {
                        echo "<td draggable='true' class='listener cursor-pointer ".$bg_color." ".$colors_classes[3]."' id=".$day.'-'.$row['id']."></td>";
                    }
                    else 
                    {
                        echo "<td draggable='true' class='listener cursor-pointer ".$bg_color."' id=".$day.'-'.$row['id']."></td>";
                    }
                    $counter++;
                }
                //echo "<td class='cursor-pointer' id=".$row['id']." style='background-color:rgb(".$colors[0].")'>dez</td>";
                echo "</tr>";

                $found_days_array = [];
                $handler = 0;
                $counter = 0;
            }

        ?>
        </table>

        <div class="grafik-break"></div>

        <div id="grafik-show-content" class="grafik-show-content">
            
            <div id="grafik-toggler" class="text-center">
                
            </div>

            <script>

            function pokazContent() {
                var e = $("#grafik-show-content").children(".grafik-second-table");
                $(e).toggleClass("dnone");
            }

            var show_content = document.getElementById("grafik-toggler");
            show_content.addEventListener('click',pokazContent);
            
            </script>

            <div class="grafik-second-table dnone">

                <table class="grafik-table">
                    <tr>

                    </tr>
                </table>

            </div>

        </div>

	</div>

    <script>
        var color_elem;
        var months = ["sty", "lut", "mar", "kwi", "maj", "cze", "lip", "sie", "wrz", "paź", "lis", "gru"];
        var x;

        var first_e;
        var first_e_day;
        var first_e_id;
        var second_e;
        var second_e_day;
        var second_e_id;

        //var first_day = <?php //echo "'$days_array[0]'"; ?>;
        //var last_day = <?php //echo "'$days_array[55]'"; ?>;

        function swapElem() {
            let temp_e = first_e;
            let temp_day = first_e_day;
            let temp_id = first_e_id;
            first_e = second_e;
            first_e_day = second_e_day;
            first_e_id = second_e_id;
            second_e = temp_e;
            second_e_day = temp_day;
            second_e_id = temp_id;
        }

        function printMonth(e) {
            return months[e-1];
        }

        function checkDays() {
            let f_date = Date.parse(first_e_day.substr(3,2)+' '+first_e_day.substr(6,2)+' '+first_e_day.substr(0,2));
            let s_date = Date.parse(second_e_day.substr(3,2)+' '+second_e_day.substr(6,2)+' '+second_e_day.substr(0,2));
            return Math.abs(f_date-s_date) > 86400000;
        }

        function checkIds() {
            return first_e_id == second_e_id;
        }

        function handleDragStart(e) {
            first_e = this;
            first_e_day = this.id.substr(0, this.id.indexOf('-'));
            first_e_id = this.id.substr(this.id.indexOf('-')+1);
        }

        function handleDragOver(e) {
            e.preventDefault();
            //console.log('handleDragOver');
        }

        function handleDragEnter(e) {
            //console.log('handleDragEnter');
        }

        function handleDragLeave(e) {
            //console.log('handleDragLeave');
        }

        function handleDragEnd(e) {
            //console.log('handleDragEnd');
        }

        function handleDrop(e) {
            second_e = this;
            second_e_day = this.id.substr(0, this.id.indexOf('-'));
            second_e_id = this.id.substr(this.id.indexOf('-')+1);

            checkElem();
        }
        
        function deleteDecision() {
            $(".schedule-background, .schedule-decision").remove();
        }

        function queryCopyCheck() {
            if (x) swapElem();
            queryCopy();
        }

        function queryFillCheck() {
            if (x) swapElem();
            queryFill();
        }

        function queryFillAllCheck() {
            if (x) swapElem();
            queryFillAll();
        }

        function queryCopy() {
            //if (confirm("Czy na pewno chcesz wykonać tę operację") == false) return;
            deleteDecision();

            var first_day = [first_e_id, first_e_day];
            var second_day = [second_e_id, second_e_day];

            first_day = JSON.stringify(first_day);
            second_day = JSON.stringify(second_day);

            $.ajax({
                url: "./grafik_query_copy.php",
                type: "get",
                data: {first_day:first_day, second_day:second_day},
                success: function (response) {
                    response = JSON.parse(response)
                    if (response[0] == 1) {
                        if (response[1] == 1) {
                            $(second_e).addClass('schedule-bg-blue');
                        } else {
                            $(second_e).removeClass('schedule-bg-blue');
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })
        }

        function queryFill() {
            //if (confirm("Czy na pewno chcesz wykonać tę operację") == false) return;
            deleteDecision();

            var first_day = [first_e_id, first_e_day];
            var second_day = [second_e_id, second_e_day];

            first_day = JSON.stringify(first_day);
            second_day = JSON.stringify(second_day);

            console.log(first_day, second_day);

            $.ajax({
                url: "./grafik_query_fill.php",
                type: "get",
                data: {first_day:first_day, second_day:second_day},
                success: function (response) {
                    response = JSON.parse(response)
                    console.log(response);
                    if (response[0] == 1) {
                        if (response[1] == 1) {
                            response[2].forEach(function(day) {
                                $("#"+day+'-'+first_e_id).addClass('schedule-bg-blue');
                            })
                        } else {
                            response[2].forEach(function(day) {
                                $("#"+day+'-'+first_e_id).removeClass('schedule-bg-blue');
                            })
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })
        }

        function queryFillAll() {
            if (confirm("Czy na pewno chcesz wykonać tę operację") == false) return;
            deleteDecision();

            var first_day = [first_e_id, first_e_day];
            var second_day = [second_e_id, second_e_day];

            first_day = JSON.stringify(first_day);
            second_day = JSON.stringify(second_day);

            console.log(first_day, second_day);

            $.ajax({
                url: "./grafik_query_fill_all.php",
                type: "get",
                data: {first_day:first_day, second_day:second_day},
                success: function (response) {
                    response = JSON.parse(response)
                    console.log(response);
                    if (response[0] == 1) {
                        if (response[1] == 1) {
                            response[2].forEach(function(day) {
                                $("#"+day+'-'+first_e_id).addClass('schedule-bg-blue');
                            })
                        } else {
                            response[2].forEach(function(day) {
                                $("#"+day+'-'+first_e_id).removeClass('schedule-bg-blue');
                            })
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })
        }  

        function checkElem() {
            x = 0;

            if (first_e == second_e) return;

            if (checkIds() && checkDays()) {

                if (first_e_day > second_e_day) {
                    swapElem();
                    x = 1;
                }

                let scheduleBackground = document.createElement("div");
                $(scheduleBackground).addClass("schedule-background");

                let indexMain = document.createElement("div");
                $(indexMain).addClass("schedule-decision");

                let indexContainer = document.createElement("div");
                $(indexContainer).addClass("schedule-container");

                let indexHeadline = document.createElement("div");
                $(indexHeadline).addClass("schedule-headline");
                let first_date_long = first_e_day.substr(6,2) + " " + printMonth(parseInt(first_e_day.substr(3,2))) + " 20" + first_e_day.substr(0,2);
                let second_date_long = second_e_day.substr(6,2) + " " + printMonth(parseInt(second_e_day.substr(3,2))) + " 20" + second_e_day.substr(0,2);
                $(indexHeadline).html(first_date_long + "&nbsp;&nbsp;-&nbsp;&nbsp;" + second_date_long);

                let indexHeadlineDel = document.createElement("div");
                $(indexHeadlineDel).addClass("schedule-decision-del");
                $(indexHeadlineDel).html("&#10005");
                indexHeadlineDel.addEventListener('click', deleteDecision);

                let indexLinks = document.createElement("div");
                $(indexLinks).addClass("schedule-links");

                // przyciski

                let linkQueryCopy = document.createElement("div");
                $(linkQueryCopy).addClass("schedule-link-div");
                linkQueryCopy.addEventListener('click', queryCopyCheck);
                let linkQueryCopyInside = document.createElement("div");
                let linkQueryCopyInsideText;
                if (x) {
                    linkQueryCopyInsideText = second_date_long + " do " + first_date_long;
                } else {
                    linkQueryCopyInsideText = first_date_long + " do " + second_date_long;
                }
                $(linkQueryCopyInside).addClass("schedule-link-inside").html("Kopiowanie tylko dla tego dnia ("+ linkQueryCopyInsideText +")");
                $(linkQueryCopy).append($(linkQueryCopyInside));

                let linkQueryFill = document.createElement("div");
                $(linkQueryFill).addClass("schedule-link-div");
                linkQueryFill.addEventListener('click', queryFillCheck);
                let linkQueryFillInside = document.createElement("div");
                $(linkQueryFillInside).addClass("schedule-link-inside").html("Kopiowanie dla danego i wszystkich poprzednich dni (bez weekendów)");
                $(linkQueryFill).append($(linkQueryFillInside));

                let linkQueryFillAll = document.createElement("div");
                $(linkQueryFillAll).addClass("schedule-link-div");
                linkQueryFillAll.addEventListener('click', queryFillAllCheck);
                let linkQueryFillAllInside = document.createElement("div");
                $(linkQueryFillAllInside).addClass("schedule-link-inside").html("Kopiowanie dla danego i wszystkich poprzednich dni (z weekendami)");
                $(linkQueryFillAll).append($(linkQueryFillAllInside));

                // koniec przycisków

                $(indexLinks).append($(linkQueryCopy)).append($(linkQueryFill)).append($(linkQueryFillAll));
                $(indexHeadline).append($(indexHeadlineDel));
                $(indexContainer).append($(indexHeadline)).append($(indexLinks));
                $(indexMain).append($(indexContainer));

                $("body").append(scheduleBackground).append(indexMain);
            } else {
                queryCopy();
            }
        }

        function handleClick() {

            string = $(this).attr('id')
            id = string.substr(string.indexOf('-')+1)
            day = string.substr(0,string.indexOf('-'))

            $.ajax({
                url: "./grafik_show.php",
                type: "get",
                data: {id:id,day:day},
                success: function (response) {
                    //console.log(response);
                    $(document.body).append(
                        $(document.createElement('div'))
                        .addClass('align-projects-week-bg'),

                        $(document.createElement('div'))
                        .addClass('align-projects-week')
                        .append(
                            $(document.createElement('div'))
                            .html(response)
                        )
                        .append(
                            $(document.createElement('div'))
                            .html('&#10006;')
                            .addClass('align-projects-week-del')
                            .attr('onclick','plan_close(this)')
                            .attr('id',id+","+day)
                        )
                    )
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })

        }

        let items = document.querySelectorAll('.listener');
        items.forEach(function (item) {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('dragenter', handleDragEnter);
            item.addEventListener('dragleave', handleDragLeave);
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('drop', handleDrop);
            item.addEventListener('click', handleClick);
        });

        plan_close = (e) => {

            var daily_array = [];

            var table_info = JSON.stringify(e.id.split(','));

            $(".align-projects-week tr[id]").each(function() {
                if ($(this).find("select").val()) {
                    var string = $(this).find("input[type=text]").val()
                    var row = {id:this.id, importance:$(this).find("select").val(), note:string};
                    daily_array.push(row)
                }
            })

            daily_array = JSON.stringify(daily_array)

            let td_id = e.id.substr(e.id.indexOf(',')+1);
            td_id += '-'+e.id.substr(0,e.id.indexOf(','));

            $.ajax({
                url: "./grafik_insert.php",
                type: "get",
                data: {array:daily_array, table_info:table_info},
                success: function (response) {
                    if (response==1) {
                        if ($("#"+td_id).hasClass('schedule-bg-blue')) return
                        $("#"+td_id).addClass('schedule-bg-blue')
                    } else {
                        if (!$("#"+td_id).hasClass('schedule-bg-blue')) return
                        $("#"+td_id).removeClass('schedule-bg-blue')
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            })

            $(e).parent().siblings(".align-projects-week-bg").remove()
            $(e).parent().remove()
        }

    </script>

</body>
</html>
