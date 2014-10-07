<?php
class TermParents extends xPDOValidationRule {
    public function isValid($value, array $options = array()) {
        parent::isValid($value, $options);
        $result = false;
        $obj=& $this->validator->object;
        $xpdo=& $obj->xpdo;

        $validParentClasses = array('Taxonomy','Term');  //
        if ($obj->Parent && in_array($obj->Parent->class_key, $validParentClasses)) {
           $result = true; 
        }
        if ($result === false) {
            $xpdo->log(\modX::LOG_LEVEL_ERROR, 'Taxonomy Term validation error: a Term may only exist as a child of '.implode(', ',$validParentClasses));
            $this->validator->addMessage($this->field, $this->name, $this->message);
        }
 
        return $result;
    }
}