

<!-- dataform begin //-->
<div class="dataform">
<?php echo $form_begin?>

<?php if($label!=""):?>
  <div class="df_header">
    <div class="df_label"><?php echo $label?></div>
    <div class="df_buttons_tr"><?php echo $container["TR"]?></div>
    <div style="clear:both"></div>
  </div>
<?php endif;?>

<div class="df_body">
<?php if(isset($message))://show message?>
<div class="message"><?php echo $message?></div>
<?php else://show form?>
<?php if($error_string):?><div class="alert"><?php echo nl2br($error_string)?></div><?php endif;?>
<?php

  //prepare nested fields
  foreach ($fields as $field_name => $field_ref)
  {
    if (isset($field_ref->in))
    {
      if (isset($series_of_fields[$field_ref->in][0]) && $field_ref->label!="")
        $series_of_fields[$field_ref->in][0]->label .= '/'. $field_ref->label;
      $series_of_fields[$field_ref->in][] = $field_ref;

    }
    else
    {
      $series_of_fields[$field_name][] = $field_ref;
    }
  }

  //prepare grouped fields
  $ordered_fields = array();
  foreach ($fields as $field_name => $field_ref)
  {
    if (!isset($field_ref->in))
    {
      if (isset($field_ref->group))
      {
        $ordered_fields[$field_ref->group][$field_name] = $series_of_fields[$field_name];
      }
      else
      {
        $ordered_fields["ungrouped"][$field_name] = $series_of_fields[$field_name];
      }
    }
  }
  unset($series_of_fields);



  //build main array
  $grps = array();
  foreach ($ordered_fields as $group=>$series_of_fields)
  {
    unset($gr);

    $gr["group_name"] = $group;

    foreach ($series_of_fields as $series_name=>$serie_fields)
    {
      unset($sr);
      $sr["is_hidden"] = false;
      $sr["series_name"] = $series_name;

      foreach ($serie_fields as $field_ref)
      {
        unset($fld);
        if (($field_ref->on("hidden") || $field_ref->visible === false || in_array($field_ref->type, array("hidden","auto"))))
        {
          $sr["is_hidden"] = true;
        }

        $fld["label"] = $field_ref->label;
        $fld["id"] = $field_ref->name;
        $fld["field"] = $field_ref->output;
        $fld["type"] = $field_ref->type;
        $fld["star"] = $field_ref->star;
        $sr["fields"][] = $fld;
      }
      $gr["series"][] = $sr;

    }
    $grps[] = $gr;

  }
  $groups = $grps;

?>
<?php if (isset($groups)):?>
<?php foreach ($groups as $group)://groups?>
<?php if ($group["group_name"] != "ungrouped"):?>
<fieldset id="group_<?php echo strtolower(preg_replace('/[^A-Za-z0-9_]*/', '', $group["group_name"]));?>">
    <legend><?php echo $group["group_name"]?></legend>
<?php endif;?>
<?php foreach ($group["series"] as $field_series)://field_series?>
<?php if($field_series["is_hidden"]):?>
<?php foreach ($field_series["fields"] as $field):?>
 <?php echo $field["field"]?>
<?php endforeach;?>
<?php else://not hidden?>
<div class="serie">
<?php if(isset($field_series["fields"])): ?>
<?php $first_field=true?>
<?php foreach ($field_series["fields"] as $field)://fields?>
<?php if($first_field):?>
<?php $first_field=false?>
<?php if (($field["type"] == "container")||($field["type"] == "iframe")):?>
<a name="anchor_<?php echo $field["id"];?>"></a>
<div class="field" id="<?php echo $field["id"];?>">
<?php echo $field["field"]?>
<?php else:?>
<label for="<?php echo $field["id"];?>"><?php echo $field["label"].$field["star"]?></label>
<div class="field" id="div_<?php echo preg_replace('/[^A-Za-z0-9_]*/', '', $field["id"]);?>">
<?php echo $field["field"]?>
<?php endif;?>
<?php else:?>
<?php echo $field["field"]; //since the second field.. no label was displayed?>
<?php endif;?>
<?php endforeach;//fields?>
<?php endif;?>
</div>
<div style="clear:both"></div>
</div>
<?php endif;//hidden?>
<?php endforeach;//field_series?>
<?php if ($group["group_name"] != "ungrouped"):?>
</fieldset>
<?php endif;?>
<?php endforeach;//groups?>
<?php endif;?>
<?php endif;//end message or form?>
</div>

<?php echo $form_scripts?>
<div class="df_footer">
<div class="df_buttons_bl"><?php echo $container["BL"]?></div>
<div class="df_buttons_br"><?php echo $container["BR"]?></div>
<div style="clear:both"></div>
</div>


<br />
<?php echo $form_end?>
</div>
<!-- dataform end //-->
