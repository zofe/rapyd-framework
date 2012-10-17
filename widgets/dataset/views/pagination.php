


  <ul>
<?php if ($first_page): ?>
  <li><a href="<?php echo str_replace('{page}', 1, $url) ?>">&lsaquo;&nbsp;<?php echo rpd::lang('pag.first')?></a></li>
<?php endif ?>
<?php if ($previous_page): ?>
  <li><a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&lt;</a></li>
<?php endif ?>
<?php for ($i = $nav_start; $i <= $nav_end; $i++): ?>
  <?php if ($i == $current_page): ?>
  <li class="active"><a href="#"><?php echo $i ?></a></li>
  <?php else: ?>
    <li><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></li>
  <?php endif ?>
<?php endfor ?>
<?php if ($next_page): ?>
  <li><a href="<?php echo str_replace('{page}', $next_page, $url) ?>">&gt;</a></li>
<?php endif ?>
<?php if ($last_page): ?>
  <li><a href="<?php echo str_replace('{page}', $last_page, $url) ?>"><?php echo rpd::lang('pag.last')?> &nbsp;&rsaquo;</a></li>
<?php endif ?>
  </ul>