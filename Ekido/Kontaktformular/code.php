<div style="margin-top:10px;">
  <div class="row" id="privacy-security">
   <label for="inlineCheckbox11" class="col-sm-3 control-label">Datenschutz: *</label>
   <label class="checkbox-inline">
    <?php if ($fehler["datenschutz"] != "") { echo $fehler["datenschutz"]; } ?>
  <input type="checkbox" id="inlineCheckbox11" name="datenschutz" value="Gelesen und akzeptiert." <?php if ($_POST['datenschutz']=='Gelesen und akzeptiert.') echo(' checked="checked" '); ?>> <a href="<?php echo "$datenschutzerklaerung"; ?>" target="_blank">Bitte lesen und akzeptieren Sie die Datenschutzerkl&auml;rung.</a>
</label>


  </div></div>