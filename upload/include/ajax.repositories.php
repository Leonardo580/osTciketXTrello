<?php
class RepositoriesAjaxAPI extends AjaxController{
    function add($rid=0, $vars=array()){
        global $thisstaff;
        if ($rid){
            $vars= array_merge($_SESSION[':form:data'], $vars);
            $originalTask= Task::lookup($rid);
        }
        else
            unset($_SESSION[':form-data']);
        $info=$errors=array();
        if ($_POST){
            Draft::deleteForNamespace("repositories.add", $thisstaff->getId());
            //default form
            $form = RepositoriesForm::getInstance();
            $form->setSource($_POST);
            //internal form
            $iform= RepositoriesForm::getInternalForm($_POST);
            $isvalid=true;
            if (!$iform->isValid())
                $isvalid=false;
            if (!$form->isValid())
                $isvalid=false;
            if (!$isvalid){
                $vars=$_POST;
                $vars["default_formdata"]=$form->getClean();
                $vars["internal_formdata"]=$iform->getClean();
                $desc =$form->getField('description');
                $vars['description']=$form->getClean();
                $vars['staffId']= $thisstaff->getId();
                $vars['poster']= $thisstaff;
                $vars['ip_address']=$_SERVER['REMOTE_ADDR'];

            }
        }
        include STAFFINC_DIR . 'templates/Repositories.tmpl.php';
    }
}