<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php echo $this->getMeta() ?>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
</head>

<body>
<header>
    <h1 class="text-center text-white bg-primary">microfw</h1>
</header>

<main>
    <div class="d-flex container flex-column align-items-center text-start fs-4">
        <?php if (isset($content)) echo $content; ?>
    </div>
</main>

<footer>

</footer>
<?php if (isset($this->scripts)) {
    foreach ($this->scripts as $script) {
        print_r($script);
    }
} ?>

</body>
</html>