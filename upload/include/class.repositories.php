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
class Repositories extends RepositoriesModel implements Threadable {
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

    static function getAllRepositories(){
        $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
        if (!$link)
            die("Error: Unable to connect to MySQL." . PHP_EOL);
        $sql = "select id, title, description, creator, dateCreated from repos";
        $result = mysqli_query($link, $sql);
        $repositories = array();
        while($row = mysqli_fetch_array($result)){
            $repositories[] = $row;
        }
        mysqli_close($link);
        return $repositories;

    }
    function getDynamicFieldById($fid){
        foreach (DynamicFormEntry::forObject($this->getId(),
        ObjectModel::OBJECT_TYPE_TASK) as $form){
            foreach ($form->getFields() as $field)
                if ($field->getId() ==$fid)
                    return $field;
        }
    }
    function getDynamicFields($criteria=array()) {

        $fields = DynamicFormField::objects()->filter(array(
            'id__in' => $this->entries
                ->filter($criteria)
                ->values_flat('answers__field_id')));

        return ($fields && count($fields)) ? $fields : array();
    }
    function getField($fid){
        if (is_numeric($fid))
            return $this->getDynamicFieldById($fid);
        switch ($fid){
            case "dateCreated":
                return  DateTimeField::init(array(
                    "id"=>$fid,
                    "name"=>$fid,
                    "default"=> Misc::db2gmtime($this->getDateCreated()),
                    "label"=> __("Due Date"),
                    "configuration"=> array(
                        "min"=> Misc::gmtime(),
                        "time"=> true,
                        "gmt"=> false,
                        "future"=> true,
                    )
                ));
        }
    }
    function getThreadId(){
        return $this->thread->getId();
    }
    function getThread(){
        return $this->thread();
    }
    function getThreadEntry($id){
        return $this->thread->getEntry($id);
    }
    function getThreadEntries($type=false){
        $thread = $this->getThread()->getEntries();
        if ($type && is_array($type))
            $thread->filter(array('type__in'=>$type));
        return $thread;

    }
    function postThreadEntry($type, $vars, $options=array()){
        $errors = array();
        $poster = isset($options['poster']) ? $options['poster'] : null;
        $alert = isset($options['alert']) ? $options['alert'] : true;
        switch ($type) {
            case 'N':
            case 'M':
                return $this->getThread()->addDescription($vars);
                break;
            default:
                return $this->postNote($vars, $errors, $poster, $alert);
        }
    }
    function getForm(){
        if (!isset($this->form)) {
            // Look for the entry first
            if ($this->form = DynamicFormEntry::lookup(
                array('object_type' => ObjectModel::OBJECT_TYPE_REPOSITORIES))) {
                return $this->form;
            }
            // Make sure the form is in the database
            elseif (!($this->form = DynamicForm::lookup(
                array('type' => ObjectModel::OBJECT_TYPE_REPOSITORIES)))) {
                $this->__loadDefaultForm();
                return $this->getForm();
            }
            // Create an entry to be saved later
            $this->form = $this->form->instanciate();
            $this->form->object_type = ObjectModel::OBJECT_TYPE_REPOSITORIES;
        }

        return $this->form;
    }
    function addDynamicData($data){
        $rf= RepositoriesForm::getInstance($this->id, true);
        foreach ($rf->getFields() as $f)
            if (isset($data[$f->get('name')]))
                $rf->setAnswer($f->get('name'), $data[$f->get('name')]);

        $rf->save();

        return $rf;
    }

    function getDynamicData($create=true) {
        if (!isset($this->_entries)) {
            $this->_entries = DynamicFormEntry::forObject($this->id,
                ObjectModel::OBJECT_TYPE_REPOSITORIES)->all();
            if (!$this->_entries && $create) {
                $f = RepositoriesForm::getInstance($this->id, true);
                $f->save();
                $this->_entries[] = $f;
            }
        }

        return $this->_entries ?: array();
    }
    function to_json(){
        $info =array(
            'id'=> $this->getId(),
            "title"=> $this->getTitle(),
        );
        return JsonDataEncoder::encode($info);
    }
    function __cdata($field, $ftype=null){
        foreach($this->getDynamicData() as $e){
            if (!$e->form ||
                ($ftype && $ftype != $e->form->get('type')))
                continue;
            if ($a=$e->getAnswer($field))
                return $a;
        }
        return null;
    }
    function __toString() {
        return (string) $this->getTitle();
    }
    function replaceVars($input , $vars=array()){
        global $ost;
        return $ost->replaceTemplateVariables($input,
        array_merge($vars, array('task'=>$this)));

    }
    function getVar($tag){
        global $cfg;
        if ($tag && is_callable(array($this, 'get', ucfirst($tag))))
            return call_user_func(array($this, 'get', ucfirst($tag)));
        switch(mb_strtolower($tag)){
            case "title":
                return $this->getTitle();
                case "description":
                return Format::display($this->getDescription());
            case "dateCreated":
                return new FormattedDate($this->getDateCreated());

        }
        return false;
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