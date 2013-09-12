<dl>
    <dt>Name:</dt>
    <dd id="widget_name"><?php echo $widget; ?></dd>

    <dt>Size:</dt>
    <dd><select id="widget_size"><?php foreach ($availableSizes as $_size): ?>
            <option value="<?php echo $_size[0]; ?>_<?php echo $_size[1]; ?>"<?php
                if ($_size[0] == $size[0] && $_size[1] == $size[1]) {
                    echo ' selected="selected"';
                }
                ?>><?php echo $_size[0]; ?>*<?php echo $_size[1]; ?></option>
            <?php endforeach; ?></select></dd>
    <?php
    foreach ($params as $key => $value) {
        echo '<dt>';
        switch ($value['type']) {
            case 'project':
                echo 'Project';
                break;

            case 'build':
                echo 'Build';
                break;

            default:
                echo $value['title'];
                break;
        }
        echo ':</dt><dd>';
        switch ($value['type']) {
            case 'project':
                echo <<<EOT
<select id="widget_project"></select>
<script type="text/javascript">
    var updateProjectList = function() {
        var o = \$("#widget_project");
        if (o.size() > 0) {
            o.empty();
EOT;
                if (!isset($value['required']) || !$value['required']) {
                    echo 'o.append(\'<option value="">None or default</option>\');';
                }
                if (isset($value['default'])) {
                    $def = '\' + (data[i].id == ' . $value['default'] . ' ? \' selected=\"selected\"\' : \'\') + \'';
                } else {
                    $def = '';
                }
                echo <<<EOT
            \$.get('api/project/list', function(data) {
                $.each(data, function(i) {
                    o.append('<option value="' + data[i].id + '"$def>' + data[i].name + '</option>');
                });
                o.trigger("change");
            }, "json");
        }
    };
    updateProjectList();
</script>
EOT;
                break;

            case 'build':
                echo <<<EOT
<select id="widget_build"></select>
<script type="text/javascript">
    var updateBuildList = function() {
        var o = \$("#widget_build");
        if (o.size() > 0) {
            o.empty();
EOT;
                if (!isset($value['required']) || !$value['required']) {
                    echo 'o.append(\'<option value="">None or default</option>\');';
                }
                echo <<<EOT
            \$.get('api/build/list/' + \$("#widget_project").val(), function(data) {
                $.each(data, function(i) {
                    o.append('<option value="' + data[i].id + '">' + data[i].revision + ' (' + data[i].status + ')</option>');
                });
            }, "json");
        }
    };
    \$("#widget_project").change(updateBuildList);
</script>
EOT;
                break;

            case 'enum':
                echo '<select id="widget_' . $key . '">';
                foreach ($value['enum'] as $enumValue) {
                    echo '<option value="' . $enumValue . '"';
                    if (isset($value['default']) && $value['default'] == $enumValue) {
                        echo ' selected="selected"';
                    }
                    echo '>' . $enumValue . '</option>';
                }
                echo '</select>';
                break;

            default:
                echo '<input type="text" id="widget_' . $key . '"';
                if (isset($value['default'])) {
                    echo ' value="' . $value['default'] . '"';
                }
                echo '/>';
        }
        echo '</dd>';
    }
    ?>
</dl>
<hr/>
<span id="widget_drag" class="ui-button ui-button-primary">Drag me!</span>
<hr/>
<button id="widget_hide">Back</button>

<script type="text/javascript">
    $("#widget_hide").button().click(function() {
        $("#list_widgets").show('slide', {direction: 'left'}, 500);
        $("#widget_details").fadeOut(500);
        $.owaka.designer.slots.hide();
    });

    $("#widget_drag").draggable({
        appendTo: "body",
        helper: "clone",
        start: function(event, ui) {
            var params = {};
<?php foreach ($params as $key => $value): ?>
                params.<?php echo $key; ?> = $("#widget_<?php echo $key; ?>").val();
<?php endforeach; ?>
            $("#widget_drag").data('params', params);
        }
    });
    $("#widget_size").change(function() {
        $.owaka.designer.widget.prepareToAdd();
    });

    $("#list_widgets").hide("slide", {direction: 'left'}, 500);
    $("#widget_details").fadeIn(500);


    $.owaka.designer.widget.prepareToAdd();
    $("#widget_drag").attr("data-widget", '<?php echo $widget; ?>');
</script>