<?php
$image = (isset($attributes['imageId']) ? rig_image($attributes['imageId'], 'full') : '');
$css = $image ? 'style="background-image: url(\'' .  $image . '\')"' : '';
?>

<div <?= $css; ?>>
  <?= $content; ?>
</div>