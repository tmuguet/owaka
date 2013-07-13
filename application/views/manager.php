<?php
$title = ($project->loaded() ? 'Edit project ' . $project->name : 'Add a new project');

$menu = array();

$menu_bottom   = array();
$menu_bottom[] = array(
    'title' => 'cancel',
    'href'  => 'dashboard/main',
    'img'   => 'freepik/powerbutton',
    'alt'   => 'Cancel'
);

echo View::factory('baseStart')
        ->set('title', $title)
        ->render();
echo View::factory('baseMenu')
        ->set('title', $title)
        ->set('menu', $menu)
        ->set('menu_bottom', $menu_bottom)
        ->render();
?>
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
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();