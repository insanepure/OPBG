<form method="post" action="?p=itemforall&use=inventory">
    <div class="spacer"></div>
    <select class="select" name="item" style="width:200px;">
        <?php
            $items = new Generallist($database, 'items', '*', '', '', 99999999999, 'ASC');
            $id = 0;
            $entry = $items->GetEntry($id);
            while ($entry != null)
            {
                ?>
                    <option value="<?php echo $entry['id']; ?>"><?php echo $entry['name'] . '(' . $entry['id'] . ')'; ?></option>
                <?php
                ++$id;
                $entry = $items->GetEntry($id);
            }
        ?>
    </select>
    <div class="spacer"></div>
    <input type="text" placeholder="Menge" name="amount" />
    <div class="spacer"></div>
    <input type="text" placeholder="Leer" name="userID" />
    <div class="spacer"></div>
    <input type="submit" value="Verteilen" />
</form>
<div class="spacer"></div>
<div class="spacer"></div>
<b>Info: bei absenden wird dieses Item an jeden User gesendet.</b>
