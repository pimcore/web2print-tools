<?php
/**
 * @var $this Pimcore_View
 * @var $brick Document_Tag_Area_Info
 */
$brick = $this->brick;

if (!(is_array($this->placeholder("__areas")->getValue()) and in_array($brick->getId(),$this->placeholder("__areas")->getValue()))) {
    $this->placeholder("__areas")->append($this->brick->getId());

    $this->headLink()->prependStylesheet(
        array(
            'href' => $brick->getPath() . '/frontend.css',
            'rel' => 'stylesheet',
            'media' => 'all',
            'type' => 'text/css'
        )
    );

    if($this->editmode) {
        $this->headLink()->prependStylesheet(
            array(
                'href' => $brick->getPath() . '/editmode.css',
                'rel' => 'stylesheet',
                'media' => 'all',
                'type' => 'text/css'
            )
        );
    }
}
?>



<?php if($this->editmode) { ?>

    <?php echo $this->customareatable("table", array("class" => $this->selectedClassName)) ?>

    <?php $this->template("editmode.php"); ?>

<?php } else { ?>

    <?php $this->template("frontend.php"); ?>

<?php } ?>
