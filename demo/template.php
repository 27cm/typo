<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Typo <?php if (isset($typo)) { echo $typo->getVersion(); } ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style>
        ins, del {
            text-decoration: none;
            display: inline-block;
            padding: 0 2px;
            margin: 0 1px;
        }
        ins {
            background-color: rgb(135, 187, 135);
        }
        del {
            background-color: rgb(255, 134, 134);
            color: rgb(185, 45, 45);
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (isset($error)) : ?>
    <div class="alert alert-danger fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong>Ошибка (404): </strong><?php echo htmlspecialchars($error, ENT_QUOTES, 'utf-8'); ?> <a class="btn btn-danger" href="mailto:#">Сообщить об ошибке</a>
    </div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <textarea class="form-control" name="text" rows="10"><?php if(isset($input)) echo htmlspecialchars($input, ENT_QUOTES, 'utf-8'); ?></textarea>
        </div>
        <div class="form-group">
            <a class="btn btn-link" data-toggle="collapse" href="#settings">Настройки</a>
            <button type="submit" class="btn btn-success btn-lg">Сделать хорошо</button>
        </div>
        <div id="settings" class="collapse">
            <ul class="nav nav-tabs">
                <?php

                $i = 1;
                foreach ($typo->getModules() as $module) {
                    if ($i == 1) {
                        echo '<li class="active">';
                    } else {
                        echo '<li>';
                    }

                    preg_match('~^Wheels\\\\Typo\\\\Module\\\\(.+)\\\\[^\\\\]+$~', get_class($module), $matches);
                    $name = $matches[1];
                    echo '<a href="#tab' . $i . '" data-toggle="tab">' . $name . '</a>';

                    echo '</li>';
                    $i++;
                }

                ?>
            </ul>
            <div class="tab-content">
                <?php

                $i = 1;
                foreach($typo->getModules() as $module) {
                    if ($i == 1) {
                        echo '<div id="tab' . $i . '" class="tab-pane active">';
                    } else {
                        echo '<div id="tab' . $i . '" class="tab-pane">';
                    }

                    foreach ($module->getConfig()->getOptions() as $option) {
                        $name = $option->getName();
                        $type = $option->getType();
                        $allowed = $option->getAllowed();
                        $value = $option->getValue();
                        $desc = $option->getDesc();

                        echo '<div class="form-group">';
                        if ($type instanceof Wheels\Config\Option\Type\Tbool) {
                            echo '<div class="checkbox">';
                            echo '<label>';
                            if ($value) {
                                echo '<input type="checkbox" name="' . $name . '" checked="checked">';
                            } else {
                                echo '<input type="checkbox" name="' . $name . '">';
                            }
                            echo ' ' . $desc;
                            echo '</label>';
                            echo '</div>';
                        } elseif (!empty($allowed)) {
                            echo '<label class="control-label">' . $desc . '</label>';
                            foreach ($allowed as $v) {
                                echo '<div class="radio">';
                                echo '<label>';
                                if ($v === $value) {
                                    echo '<input type="radio" name="' . $name . '" value="' . $v . '" checked="checked">';
                                } else {
                                    echo '<input type="radio" name="' . $name . '" value="' . $v . '">';
                                }
                                echo ' ' . $v;
                                echo '</label>';
                                echo '</div>';
                            }
                        } else {

                            if ($type instanceof Wheels\Config\Option\Type\Tarray) {
                                $value = json_encode($value);
                            }
                            echo '<label for="' . $name . '" class="control-label">' . $desc . '</label>';
                            echo '<input type="text" id="' . $name . '" class="form-control" value="' . htmlentities($value, ENT_QUOTES, 'utf-8') . '">';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                    $i++;
                }

                ?>
            </div>
        </div>
    </form>
    <div><?php /* if(isset($output)) echo $output; */ ?></div>
    <div><?php echo $diff->renderDiffToHTML2(); ?></div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    jQuery(function($) {
        $('.alert').alert();

        $('.nav-tab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
</body>
</html>