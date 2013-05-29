<?php echo $class ?> [ <?php echo $code ?> ]: <?php echo $message; ?>

<?php echo Debug::path($file) ?> [ <?php echo $line ?> ]

<?php foreach (Debug::trace($trace) as $i => $step): ?>
    <?php if ($step['file']): ?>
        <?php echo Debug::path($step['file']) ?> [ <?php echo $step['line'] ?> ]
    <?php else: ?>
        {<?php echo __('PHP internal call') ?>}
    <?php endif ?>
    <?php echo $step['function'] ?>(<?php if ($step['args']): ?><?php echo __('arguments') ?><?php endif ?>)

<?php endforeach; ?>