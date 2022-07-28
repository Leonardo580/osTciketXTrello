<?php

class RepositoriesModel extends VerySimpleModel{
    static $meta =array(
        'table' => REPOSITORIES_TABLE,
        'pk' => 'id',
        'joins'=> array()

    );
    const PERM_CREATE   = 'repositories.create';
    const PERM_EDIT     = 'repositories.edit';
    //const PERM_ASSIGN   = 'repositories.assign';
   // const PERM_TRANSFER = 'repositories.transfer';
    //const PERM_REPLY    = 'repositories.reply';
   // const PERM_CLOSE    = 'repositories.close';
    const PERM_DELETE   = 'repositories.delete';

    static protected $perms= array(
        self::PERM_CREATE    => array(
            'title' =>
            /* @trans */ 'Create',
            'desc'  =>
            /* @trans */ 'Ability to create tasks'),
        self::PERM_EDIT      => array(
            'title' =>
            /* @trans */ 'Edit',
            'desc'  =>
            /* @trans */ 'Ability to edit tasks'),
        self::PERM_DELETE    => array(
            'title' =>
            /* @trans */ 'Delete',
            'desc'  =>
            /* @trans */ 'Ability to delete tasks'),
    );
    protected function hasFlag($flag){
        return ($this->get('flags') & $flag) !== 0;
    }
    protected function clearFlag($flag){
        return $this->set('flags', $this->get('flags') & ~$flag);
    }
    protected function setFlag($flag){
        return $this->set('flags', $this->get('flags') | $flag);
    }
    function getId(){
        return $this->id;
    }
    function getTitle(){
        return $this->title;
    }
    function getDateCreated(){
        return $this->dateCreated;
    }
    function getDescription(){
        return $this->description;
    }
    /*function getCreator(){
        return $this->creator;
    }
    function getMembers(){
        return $this->members;
    }*/


}
class Repositories extends RepositoriesModel{
    var $form;
    var $entry;
    var $_thread;
    var $_entries;
    var $_answers;
    var $lastrespondent;

    function __onload()
    {
       $this->loadDynamicData();
    }
    function loadDynamicData() {
        if (!isset($this->_answers)) {
            $this->_answers = array();
            foreach (DynamicFormEntryAnswer::objects()
                         ->filter(array(
                             'entry__object_id' => $this->getId(),
                             'entry__object_type' => ObjectModel::OBJECT_TYPE_TASK
                         )) as $answer
            ) {
                $tag = mb_strtolower($answer->field->name)
                    ?: 'field.' . $answer->field->id;
                $this->_answers[$tag] = $answer;
            }
        }
        return $this->_answers;
    }
    static function __loadDefaultForm(){
        require_once INCLUDE_DIR.'class.i18n.php';
        $i18n = new Internationalization();
        $tpl = $i18n->getTemplate('form.yaml');
        foreach ($tpl->getData() as $f){
            if ($f['type'] == ObjectModel::OBJECT_TYPE_REPOSITORIES){
                $form=DynamicForm::create($f);
                $form->save();
                break;
            }

        }
    }
}
class RepositoriesForm extends DynamicForm {
    static $instance;
    static $defaultForm;
    static $internalForm;
    static $form;

    static $cdata= array(
        'table'=> REPOSITORIES_CDATA_TABLE,
        'object_id'=>'repositories_id',
        'object_type'=>ObjectModel::OBJECT_TYPE_REPOSITORIES
    );

    static function objects(){
        $os= parent::objects();
        return $os->filter(
            array(
                'type'=> ObjectModel::OBJECT_TYPE_REPOSITORIES
            )
        );
    }
    static function getDefaultForm(){
        if (!isset(static::$defaultForm)){
            if (($o = static::objects()) && $o[0])
                static::$defaultForm=$o[0];

        }
        return static::$defaultForm;
    }
    static function getInstance($object_id=0, $new=false){
        if ($new || !isset(static::$instance))
            static::$instance = static::getDefaultForm()->instantiate();
        static::$instance->object_type = ObjectModel::OBJECT_TYPE_REPOSITORIES;
        if ($object_id)
            static::$instance->object_id = $object_id;
        return static::$instance;
    }
    static function getInternalForm($source=null, $option=array()){
        if (!isset(static::$internalForm)){
          static::$internalForm=new RepositoriesInternalForm($source, $option);
        }
        return static::$internalForm;
    }
}

class RepositoriesInternalForm extends AbstractForm{
    static $layout ='GridFormLayout';
    function buildFields()
    {
        $fields= array(
            'dept_id' => new DepartmentField(array(
                'id'=>1,
                'label' => __('Department'),
                'required' => true,
                'layout' => new GridFluidCell(6),
            )),
            'assignee' => new AssigneeField(array(
                'id'=>2,
                'label' => __('Assignee'),
                'required' => false,
                'layout' => new GridFluidCell(6),
            )),
            'duedate'  =>  new DatetimeField(array(
                'id' => 3,
                'label' => __('Due Date'),
                'required' => false,
                'configuration' => array(
                    'min' => Misc::gmtime(),
                    'time' => true,
                    'gmt' => false,
                    'future' => true,
                ),
            )),
        );
        $mode = @$this->options["mode"];
        if ($mode && $mode =="edit"){
            unset($fields['dept_id']);
            unset($fields['assignee']);
        }
        return $fields;
    }
}