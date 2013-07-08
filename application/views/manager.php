<!DOCTYPE html>
<html>
    <head>
        <title>owaka - <?php
            echo ($project->loaded() ? 'Edit project ' . $project->name : 'Add a new project');
            ?></title>
        <base href="/owaka/">

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" media="screen" href="css/fluid960gs/reset.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/smoothness/jquery-ui-1.10.2.custom.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/grid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="css/owaka.css" />

        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/owaka.js"></script>

        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
        <link rel="icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div id="overlay" class="ui-widget-overlay ui-helper-hidden"> </div>
        <div id="left-panel">
            <div id="owaka"><h1>owaka</h1></div>
            <div id="menu">
                <a href="dashboard/main" title="Cancel"><img src="img/freepik/powerbutton.png" width="32" alt="Cancel"/></a>
            </div>
        </div>
        <div id="top-panel">
            <h2><?php
                echo ($project->loaded() ? 'Edit project ' . $project->name : 'Add a new project');
                ?></h2>
        </div>
        <div id="grid">
            <form action="api/project/<?php
            echo ($project->loaded() ? 'edit/' . $project->id : 'add');
            ?>" method="post">
                <dl>
                    <dt>Name:</dt>
                    <dd><input type="text" name="name" value="<?php echo $project->name; ?>"/></dd>

                    <dt>SCM:</dt>
                    <dd><select name="scm">
                            <option value="git"<?php
                            echo ($project->scm == "git" ? ' selected="selected"' : '')
                            ?>>GIT</option>
                            <option value="mercurial"<?php
                            echo ($project->scm == "mercurial" ? ' selected="selected"' : '')
                            ?>>Mercurial</option>
                        </select></dd>

                    <dt>Path:</dt>
                    <dd><input type="text" name="path" value="<?php echo $project->path; ?>"/></dd>

                    <dt>Path to phing:</dt>
                    <dd><input type="text" name="phing_path" value="<?php echo $project->phing_path; ?>"/></dd>

                    <dt>Phing target for validation:</dt>
                    <dd><input type="text" name="phing_target_validate" value="<?php echo $project->phing_target_validate; ?>"/></dd>

                    <dt>Phing target for nightly:</dt>
                    <dd><input type="text" name="phing_target_nightly" value="<?php echo $project->phing_target_nightly; ?>"/></dd>

                    <dt>Path of reports:</dt>
                    <dd><input type="text" name="reports_path" value="<?php echo $project->reports_path; ?>"/></dd>
                </dl>
                <?php
                foreach ($reports as $_controller => $_reports) {
                    echo '<hr><h2>' . $_controller . '</h2><dl>';
                    foreach ($_reports as $_key => $_report) {
                        if ($project->loaded()) {
                            $value = ORM::factory('Project_Report')
                                            ->where('project_id', '=', $project->id)
                                            ->where('type', '=', strtolower($_controller) . '_' . $_key)
                                            ->find()->value;
                        } else {
                            $value = '';
                        }
                        echo '<dt>' . $_report['title'] . '</dt>';
                        echo '<dd><input type="text" name="' . strtolower($_controller) . '_' . $_key . '" value="' . $value . '"/></dd>';
                    }
                    echo '</dl>';
                }
                ?>
                <hr>
                <dl>
                    <dt>Active</dt>
                    <dd><input type="checkbox" name="is_active" value="1"<?php
                        ($project->is_active ? ' checked="checked"' : '');
                        ?>/></dd>
                </dl>
                <input type="submit" value="<?php
                echo ($project->loaded() ? 'Edit project ' . $project->name : 'Add a new project');
                ?>"/>
            </form>
        </div>
    </body>
</html>