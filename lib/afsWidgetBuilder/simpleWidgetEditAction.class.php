<?php
/**
 * This abstract class is used by AF Studio when new edit widget is created
 *
 * It guesses propel model object name and its coresponding form class from peer class
 * defined in widget xml datasource element
 *
 * It also dynamically reconfigures given model form object to use only fields
 * defined in widget xml config file
 * Validators are also replaced by sfValidatorPass
 * Basically I'm using form classes just to ease up filling propel objects with values from user
 *
 * @author Łukasz Wojciechowski
 */
abstract class simpleWidgetEditAction extends sfAction
{
    /**
     * @var afsWidgetBuilderWidget
     */
    protected $afsWBW;

    /**
     * @var BaseObject
     */
    protected $object;

    protected $widgetUri;

    /**
     * @var BaseFormPropel
     */
    protected $form;

    public function preExecute()
    {
        $this->widgetUri = $this->getModuleName().'/'.$this->getActionName();
        $this->afsWBW = new afsWidgetBuilderWidget($this->widgetUri);
        $this->afsWBW->loadXml();

        $peerClassName = $this->afsWBW->getDatasourceClassName();
        $modelClassName = constant("$peerClassName::OM_CLASS");
        $formClassName = "{$modelClassName}Form";

        $this->tryToLoadObjectFromRequest($peerClassName);

        if (!$this->object) {
            $this->createNewObject($modelClassName);
        }

        $this->createAndConfigureForm($formClassName);
    }

    function execute($request)
    {
        if ($request->isMethod('post')) {
            if ($this->processPostData()) {
                $result = array(
                    'success' => true,
                    'message'=>"Saved with success!",
                    'redirect'=>$this->widgetUri.'?id='.$this->object->getId()
                );
                return $result;
            }
        }
    }

    private function createAndConfigureForm($formClassName)
    {
        $this->form = new $formClassName($this->object);
        $vs = $this->form->getValidatorSchema();
        foreach ($vs->getFields() as $fieldName => $validator) {
            $this->form->setValidator($fieldName, new sfValidatorPass());
        }
        if (isset($this->form['id'])) {
            unset($this->form['id']);
        }
        $fieldNames = $this->afsWBW->getDefinedFieldNames();
        $this->form->useFields($fieldNames);

        // making form field default values available for widget XML config file placeholders
        foreach ($fieldNames as $fieldName) {
            $this->$fieldName = $this->object->getByName($fieldName, BasePeer::TYPE_FIELDNAME);
        }
    }

    private function createNewObject($modelClassName)
    {
        $this->object = new $modelClassName;
        $this->id = '';
    }

    private function tryToLoadObjectFromRequest($peerClassName)
    {
        if ($this->getRequest()->hasParameter('id')) {
            $objectId = $this->getRequest()->getParameter('id');
            if ($objectId > 0) {
                $this->object = call_user_func("$peerClassName::retrieveByPK", $objectId);
                $this->id = $this->object->getPrimaryKey();
            }
        }
    }

    private function processPostData()
    {
        $formData = $this->getRequest()->getParameter('edit');
        $formData = $formData[0];

        $formData = $this->changeKeysForForeignFields($formData);

        $this->form->bind($formData);
        return $this->form->save();
    }

    /**
     * Quick and dirty solution for one problem
     * Combo widgets generated by AF are posting input field named like "{$i:fieldName}_value"
     * Since we are basing functionality of this action on autogenerated forms we got extra form fields and validation process breaks
     * This method assumes that every key that ends with "_value" is a value for foreign column coming from combo field
     * Each of those keys are changes by removing "_value" suffix
     */
    private function changeKeysForForeignFields($formData)
    {
        $baseKeys = array();
        foreach ($formData as $key => $value) {
            if (substr($key, -6) != '_value') {
                continue;
            }

            $baseKey = str_replace('_value', '', $key);
            $baseKeys[] = $baseKey;
        }

        foreach ($baseKeys as $baseKey) {
            $valueForBaseKey = $formData["${baseKey}_value"];
            unset($formData["${baseKey}_value"]);
            $formData[$baseKey] = $valueForBaseKey;
        }

        return $formData;
    }
}