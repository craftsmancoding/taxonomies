<?php
class TermParents extends xPDOValidationRule {
    public function isValid($value, array $options = array()) {
        parent::isValid($value, $options);
        $result = false;
        $obj=& $this->validator->object;
        $xpdo=& $obj->xpdo;

        $validParentClasses = array('Taxonomy', 'Term');
        if ($obj->Parent && in_array($obj->Parent->class_key, $validParentClasses)) {
           $result = true; 
        }
        if ($result === false) {
            $xpdo->log(1, 'TermParents: FALSE');
            $this->validator->addMessage($this->field, $this->name, $this->message);
            // Seems to be no way to alert the UI
            //$xpdo->regClientStartupHTMLBlock('<script type="text/javascript">
            //    alert("PROBLEM!");
            //</script>');

        }
 
        return $result;
    }
}