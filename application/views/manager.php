<?php
if ($action == 'add') {
    $title = 'Add a new project';
} else if ($action == 'edit') {
    $title = 'Edit project ' . $project->name;
} else {
    $title = 'Duplicate project ' . $project->name;
}

$menu = array();

$menu_bottom = array();
if ($action == 'add') {
    $menu[] = array(
        'title' => 'Cancel',
        'href'  => 'dashboard/main',
        'img'   => 'off',
    );
} else {
    $menu[] = array(
        'title' => 'Cancel',
        'href'  => 'dashboard/project/' . $project->id,
        'img'   => 'off',
    );
}

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
    <form action="<?php echo $uri; ?>" method="post" class="ui-form" id="form-edit"> 
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
                ?>/></div>
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

            <div class="field"><label for="scm_url">URL for SCM checkout:</label>
                <input type="text" name="scm_url" id="scm_url" value="<?php echo $project->scm_url; ?>"/>
                <div class="details">URL used for checking out your project.</div>
            </div>

            <div class="field"><label for="scm_branch">SCM Branch:</label>
                <input type="text" name="scm_branch" id="scm_branch" value="<?php echo $project->scm_branch; ?>"/>
                <div class="details">Branch used in your SCM.</div>
            </div>

            <div class="field"><label for="path">Path:</label>
                <input type="text" name="path" id="path" value="<?php echo $project->path; ?>"/>
                <div class="details">Path where your project will be checked out (will be created if needed). If built remotely, path on the remote server.</div>
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
                <div class="details">SSH port of the remote server. Usually, 22.</div>
            </div>
            <div class="field"><label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo $project->username; ?>"/>
                <div class="details">SSH username on the remote server.</div>
            </div>
            <div class="field"><label for="privatekey_path">Path to the private key:</label>
                <input type="text" name="privatekey_path" id="privatekey_path" value="<?php echo $project->privatekey_path; ?>"/>
                <div class="details">Path to the RSA private key for authentication. The corresponding RSA public key must be present in the autorized keys of the remote user.</div>
            </div>
            <div class="field"><label for="public_host_key">Public host key:</label>
                <input type="text" name="public_host_key" id="public_host_key" value="<?php echo $project->public_host_key; ?>"/>
                <div class="details">Public RSA host key of the remote server. If the public host key does not match this value, builds will not be done.</div>
                <div class="details">On Linux/Unix, you can get this public key usually in <code>/etc/ssh_host_rsa_key.pub</code>.</div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Reports <label for="search">Add:</label> <input type="text" id="search"/></legend>
            <?php
            foreach ($reports as $_controller => $_reports) {
                echo '<div class="field reports" id="field-' . strtolower($_controller) . '"><strong>' . $_controller . '</strong>';
                foreach ($_reports as $_key => $_report) {
                    if ($project->loaded()) {
                        $value = ORM::factory('Project_Report')
                                        ->where('project_id', '=', $project->id)
                                        ->where('type', '=', strtolower($_controller) . '_' . $_key)
                                        ->find()->value;
                    } else {
                        $value = '';
                    }

                    echo '<div class="field report ' . (empty($value) ? 'empty' : 'non-empty') . '" id="field-' . strtolower($_controller) . '_' . $_key . '"><label for="' . strtolower($_controller) . '_' . $_key . '">' . $_report['title'] . ':</label>';
                    echo '<input type="text" name="' . strtolower($_controller) . '_' . $_key . '" id="' . strtolower($_controller) . '_' . $_key . '" value="' . $value . '"/>';
                    echo '<div class="details">' . $_report['description'] . '</div>';
                    echo '</div>';
                }

                $classname  = 'Controller_Processor_' . ucfirst($_controller);
                $parameters = $classname::parameters();

                if ($project->loaded()) {
                    $parametersValues = $classname::projectParameters($project->id);
                } else {
                    $parametersValues = $classname::projectParameters(0);
                }
                if (sizeof($parameters) > 0) {
                    echo '<div class="details"><strong><a href="javascript:void(0)" onclick="$(\'.param-' . strtolower($_controller) . '\').slideToggle();">Parameters</a></strong></div>';
                    foreach ($parameters as $_paramkey => $_param) {
                        echo '<div class="field param param-' . strtolower($_controller) . '" id="field-' . strtolower($_controller) . '_' . $_paramkey . '"><label for="' . strtolower($_controller) . '_' . $_paramkey . '">' . $_param['title'] . ':</label>';
                        echo '<input type="text" name="' . strtolower($_controller) . '_' . $_paramkey . '" id="' . strtolower($_controller) . '_' . $_paramkey . '" value="' . $parametersValues[$_paramkey] . '"/>';
                        echo '<div class="details">' . $_param['description'] . '</div>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            ?>
        </fieldset>
        <fieldset><legend>Misc</legend>
            <div class="field"><label for="is_active">Active:</label>
                <input type="checkbox" name="is_active" id="is_active" value="1"<?php
                echo ($project->is_active ? ' checked="checked"' : '');
                ?>/>
                <div class="details">Project will not be built in owaka if inactive.</div>
        </fieldset>
        <button type="submit" data-icon="icon-save"><?php echo $title; ?></button>
    </form>
    <form action="" method="post" class="ui-form" id="form-checkout" style="display: none"> 
        <button type="submit" data-icon="icon-code-fork">Checkout</button>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".field.report.empty").hide();
        $.each($(".field.reports"), function() {
            if ($(this).has('.field.report:visible').size() == 0) {
                $(this).hide();
            }
        });
        $(".field.param").hide();
    });
    $.owaka.formapi($('#form-edit'), function(data) {
        if (data.scm_status != 'ready') {
            $("#form-checkout").attr("action", "api/project/checkout/" + data.project);
            $("#form-checkout").fadeIn();
        } else {
<?php if ($action
        == 'edit'): ?>
                alert('Project updated');
<?php else: ?>
                alert('Project added');
<?php endif; ?>
            document.location.href = 'dashboard/project/' + data.project;
        }
    });

    $.owaka.formapi($('#form-checkout'), function(data) {
        alert('Project checked out!');
        document.location.href = 'dashboard/project/' + data.project;
    });

    $(document).ready(function() {
<?php
$data = array();
foreach ($reports as $_controller => $_reports) {
    foreach ($_reports as $_key => $_report) {
        $data[] = array(
            "label"    => $_report['title'],
            "category" => $_controller,
            "parent"   => 'field-' . strtolower($_controller),
            "field"    => 'field-' . strtolower($_controller) . '_' . $_key
        );
    }
}
echo 'var data = ' . json_encode($data) . ';';
?>

        $("#search").catcomplete({
            delay: 0,
            source: data,
            select: function(event, ui) {
                $("#" + ui.item.parent).slideDown();
                $("#" + ui.item.field).slideDown();
                return false;
            }
        });
    });

    $(".field.report input").blur(function() {
        if ($(this).val() == "") {
            $(this).parent().slideUp();
            if ($(this).parent().siblings('.field.report:visible').size() == 0) {
                $(this).parent().parent().slideUp();
            }
        }
    });
</script>
<?php
echo View::factory('baseEnd')
        ->set('title', $title)
        ->render();
