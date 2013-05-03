<?php
$projects = ORM::factory('Project')
        ->where('is_active', '=', '1')
        ->find_all();

$status = 'ok';

foreach ($projects as $project) {
    $build = $project->builds
            ->order_by('id', 'DESC')
            ->limit(1)
            ->find();

    if ($build->status == 'error') {
        $status = 'error';
        break;
    } else if ($build->status == 'unstable') {
        $status = 'unstable';
    }
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("body").addClass("grunge build-<?php echo $status; ?>");
    });
</script>