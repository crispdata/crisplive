<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Project;
use common\models\Page;
use common\models\Role;
use common\models\Recentchanges;

/**
 * Signup form
 */
class CreateContact extends Model {

    public $FirstName;
    public $LastName;
    public $Street;
    public $City;
    public $Email;
    public $Password;
    public $State;
    public $Zip;
    public $Mobile;
    public $ContactOffice;
    public $Fax;
    public $CreatedOn;
    public $IsActive;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['EventName', 'trim'],
            ['EventName', 'required'],
            ['EventName', 'string', 'min' => 1, 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'EventName' => 'Event Name',
            'EventLocation' => 'Event Location',
            'EventStartDate' => 'Event Start Date',
            'EventEndDate' => 'Event End Date',
        ];
    }

    /**
     * Create Project.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function create() {

        $project = new \common\models\Contact();

        $project->FirstName = $this->FirstName;
        $project->LastName = $this->LastName;
        $project->Street = $this->Street;
        $project->City = $this->City;
        $project->Email = $this->Email;
        $project->Password = $this->Password;
        $project->Mobile = $this->Mobile;
        $project->State = $this->State;
        $project->Zip = $this->Zip;
        $project->ContactOffice = $this->ContactOffice;
        $project->Fax = $this->Fax;
        $project->CreatedOn = date('Y-m-d H:i:s');
        $project->IsActive = 1;


        if ($project->save()) {
            
            //$page_status = $this->save_page($project->ProjectId, $this->PageName);
            //$role_status = $this->save_role($project->ProjectId, $this->ContactId, $this->RoleName);
            //if( $this->save_recent_chanegs($project) ){
            //	return $project;
            //}
            return true;
        }
        return false;
    }

    protected function save_page($project_id, $pages) {

        if (!empty($pages)) {

            foreach ($pages as $page) {

                $page_model = new Page();

                if (!empty($page)) {

                    $page_model->ProjectId = $project_id;
                    $page_model->PageName = $page;
                    $page_model->CreatedOn = date('Y-m-d H:i:s');
                    $page_model->IsActive = 1;

                    $page_model->save();
                }
            }
        }
        return true;
    }

    protected function save_role($project_id, $ContactId, $roleNames) {

        if (!empty($roleNames)) {

            foreach ($roleNames as $key => $roleName) {

                $role_model = new Role();

                if (!empty($ContactId[$key])) {

                    $role_model->ProjectId = $project_id;
                    $role_model->ContactId = $ContactId[$key];
                    $role_model->RoleName = $roleName;
                    $role_model->CreatedOn = date('Y-m-d H:i:s');
                    $role_model->IsActive = 1;

                    $role_model->save();
                }
            }
        }
        return true;
    }

    protected function save_recent_chanegs($project) {

        $recentchanges = new Recentchanges();

        $user = Yii::$app->user->identity;

        $recentchanges->UserId = $user->id;
        $recentchanges->ProjectId = $project->ProjectId;
        $recentchanges->Note = ucfirst($user->username) . ' has created new project > ' . $project->EventName;
        $recentchanges->CreatedOn = date('Y-m-d H:i:s');
        $recentchanges->IsDismiss = 0;
        $recentchanges->IsActive = 1;
        $recentchanges->IsViewed = 0;

        $recentchanges->save();

        return $recentchanges;
    }

}
