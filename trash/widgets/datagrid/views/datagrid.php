
<!-- datagrid begin //-->
<div class="datagrid">
<?php echo $form_begin?>
<div class="row-fluid dg_header">
    <div class="pull-left"><h4><?php if($label!='') echo $label.'&nbsp;('.(int)$total_rows.')'?></h4></div>
    <div class="pull-right"><?php echo $container["TR"]?></div>
</div>

<div class="dg_content">
      <table class="table-bordered table table-striped">
        <tr>
<?php foreach ($columns as $column)://table-header?>
<?php if (in_array($column->column_type, array("orderby","detail"))):?>
          <td class="table_header">
                <?php echo $column->label?>
                <a href="<?php echo $column->orderby_asc_url?>" class="icon-arrow-up icon-white"></a>
                <a href="<?php echo  $column->orderby_desc_url?>" class="icon-arrow-down icon-white"></a>
          </td>
<?php elseif ($column->column_type == "clean"):?>
          <td <?php echo $column->attributes?>><?php echo $column->label?></td>
<?php elseif (in_array($column->column_type, array("normal"))):?>
          <td class="table_header" <?php echo $column->attributes?>><?php echo $column->label?></td>
<?php endif;?>
<?php endforeach;//table-header?>
        </tr>
<?php if (count($rows)>0)://table-rows?>

<?php foreach ($rows as $row):?>
        <tr <?php if (isset($row[0], $row[0]['tr_attr'])) { echo $row[0]['tr_attr']; }?>>
<?php foreach ($row as $cell):?>
<?php if ($cell['type'] == "detail" OR $cell['link']!=""):?>
          <td <?php echo $cell['attributes']?> class="table_row"><a href="<?php echo $cell['link']?>" <?php if($cell['onclick']!='') echo 'onclick="'.$cell['onclick'].'"'?>><?php if($cell['img']!=""){ echo html_helper::image($cell['img'], array('style'=>'vertical-align:middle')); }?><?php echo $cell['value']?></a></td>
<?php elseif ($cell['type'] == "clean"):?>
          <td <?php echo $cell['attributes']?>><?php echo $cell['value']?></td>
<?php elseif ($cell['check'] != ""):?>
          <td <?php echo $cell['attributes']?> class="table_row"><?php echo $cell['check']?> <?php echo $cell['value']?> </td>
<?php else:?>
          <td <?php echo $cell['attributes']?> class="table_row"><?php echo $cell['value']?>&nbsp;</td>
<?php endif;?>
<?php endforeach;?>
        </tr>
<?php endforeach;?>
<?php endif;//table-rows?>
      </table>
</div>
<div class="pagination">
<?php echo $pagination;?>
</div>
    
    
<div class="row-fluid dg_footer">
    <div class="pull-left"><?php echo $container["BL"]?></div>
    <div class="pull-right"><?php echo $container["BR"]?></div>
</div>

<?php echo $hidden;?>
<?php echo $form_end?>
</div>
<!-- datagrid end //-->
