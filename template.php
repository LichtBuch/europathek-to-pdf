<?php
/**
 * @var int $start
 * @var int $end
 */
?>
<style>
    @page {
        margin: 0mm;
        margin-header: 0mm;
        margin-footer: 0mm;
    }
</style>
<?php for ($i = $start;$i <= $end;$i++):?>
	<img src="var:<?=$i?>" style="width: 210mm;height: 297mm" alt="<?=$i?>">
<?php endfor;?>