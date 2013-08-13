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
    ?>" method="post" class="ui-form"> 
        <fieldset>
            <legend>Project</legend>

            <div class="field">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?php echo $project->name; ?>"/>
                <div class="details">Name of your project, as it will appear in owaka</div>
            </div>

            <div class="field"><label for="is_remote">Remote:</label>
                <input type="checkbox" name="is_remote" id="is_remote" value="1"<?php
                echo ($project->is_remote ? ' checked="checked"' : '');
                ?>/>
                <div class="details">Project will be built on a remote server via SSH if checked.</div>

                <div class="field">
                    <label for="scm">SCM:</label>
                    <select name="scm" id="scm">
                        <option value="git"<?php
                        echo ($project->scm == "git" ? ' selected="selected"' : '')
                        ?>>GIT</option>
                        <option value="mercurial"<?php
                        echo ($project->scm == "mercurial" ? ' selected="selected"' : '')
                        ?>>Mercurial</option>
                    </select>
                </div>

                <div class="field"><label for="path">Path:</label>
                    <input type="text" name="path" id="path" value="<?php echo $project->path; ?>"/>
                    <div class="details">Path to your SCM repository. If built remotely, path on the remote server.</div>
                </div>

                <div class="field"><label for="phing_path">Path to phing project:</label>
                    <input type="text" name="phing_path" id="phing_path" value="<?php echo $project->phing_path; ?>"/>
                    <div class="details">Path to your phing project. It can be part of your SCM repository or not. If built remotely, path on the remote server.</div>
                </div>

                <div class="field"><label for="phing_target_validate">Phing target(s) for validation:</label>
                    <input type="text" name="phing_target_validate" id="phing_target_validate" value="<?php echo $project->phing_target_validate; ?>"/>
                    <div class="details">Target(s) for building your project and executing phpunit, phpdocumentor, ... There can be several targets separated by commas.</div>
                </div>

                <div class="field"><label for="reports_path">Path of reports:</label>
                    <input type="text" name="reports_path" id="reports_path" value="<?php echo $project->reports_path; ?>"/>
                    <div class="details">Path which will contain all the reports of validation. If built remotely, path on the remote server.</div>
                </div>
        </fieldset>
        <fieldset><legend>Remote connection</legend>
            <div class="field"><label for="host">Host:</label>
                <input type="text" name="host" id="host" value="<?php echo $project->host; ?>"/>
                <div class="details">Host of the remote server.</div>
            </div>
            <div class="field"><label for="port">Port:</label>
                <input type="text" name="port" id="port" value="<?php echo $project->port; ?>"/>
                <div class="details">SSH port of the remote server.</div>
            </div>
            <div class="field"><label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo $project->username; ?>"/>
                <div class="details">SSH username on the remote server.</div>
            </div>
            <div class="field"><label for="privatekey_path">Path to the private key:</label>
                <input type="text" name="privatekey_path" id="privatekey_path" value="<?php echo $project->privatekey_path; ?>"/>
                <div class="details">Path to the RSA private key for authentication.</div>
            </div>
            <div class="field"><label for="public_host_key">Public host key:</label>
                <input type="text" name="public_host_key" id="public_host_key" value="<?php echo $project->public_host_key; ?>"/>
                <div class="details">Public host key of the remote server. If the public host key does not match this value, builds will not be done.</div>
            </div>
        </fieldset>
        <?php
        foreach ($reports as $_controller => $_reports) {
            echo '<fieldset><legend>' . $_controller . '</legend>';
            foreach ($_reports as $_key => $_report) {
                if ($project->loaded()) {
                    $value = ORM::factory('Project_Report')
                                    ->where('project_id', '=', $project->id)
                                    ->where('type', '=', strtolower($_controller) . '_' . $_key)
                                    ->find()->value;
                } else {
                    $value = '';
                }
                echo '<div class="field"><label for="' . strtolower($_controller) . '_' . $_key . '">' . $_report['title'] . ':</label>';
                echo '<input type="text" name="' . strtolower($_controller) . '_' . $_key . '" id="' . strtolower($_controller) . '_' . $_key . '" value="' . $value . '"/>';
                echo '<div class="details">Optional</div>';
                echo '<div class="details">' . $_report['description'] . '</div>';
                echo '</div>';
            }
            echo '</fieldset>';
        }
        ?>
        <fieldset><legend>Misc</legend>
            <div class="field"><label for="is_active">Active:</label>
                <input type="checkbox" name="is_active" id="is_active" value="1"<?php
                echo ($project->is_active ? ' checked="checked"' : '');
                ?>/>
                <div class="details">Project will not be built in owaka if inactive.</div>
        </fieldset>
        <button type="submit"><?php
            echo ($project->loaded() ? 'Edit project ' . $project->name : 'Add new project');
            ?></button>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".ui-form :submit").button({
            icons: {
                primary: "ui-icon-disk"
            }
        });
    });
    $.owaka.formapi($('.ui-form'), function(data) {
        alert('Project updated');
        document.location = 'dashboard/project/' + data.project;
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();