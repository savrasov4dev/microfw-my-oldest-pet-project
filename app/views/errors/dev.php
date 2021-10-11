<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        .err {
            background: lightpink;
            padding-left: 20px;
            padding-right: 20px;
        }
        .h {
            /*background: lightsalmon;*/
            text-align: start;
            padding-left: 30%;
        }
        .t {
            background: #0dcaf0;
        }
    </style>
    <title>Error</title>
</head>
<body>

<br>
<br>

<? if ($response === 404): ?>
<h1 style="text-align: center"><b>404</b> ERROR</h1>
<? else: ?>
<h1 style="text-align: center">ERROR</h1>
<? endif; ?>

<h3 class="h"><u class="t">CODE NUMBER: </u><u class="err"><? echo $errno ?></u></h3>
<h3 class="h"><u class="t">STRING: </u><u class="err"><? echo $errstr ?></u></h3>
<h3 class="h"><u class="t">FILE: </u><u class="err"><? echo $errfile ?></u></h3>
<h3 class="h"><u class="t">LINE: </u><u class="err"><? echo $errline ?></u></h3>

</body>
</html>
