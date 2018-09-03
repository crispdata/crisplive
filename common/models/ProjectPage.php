<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Project model
 *
 * @property integer $ProjectId
 * @property integer $ContactId
 * @property integer $TemplateId
 * @property string $EventName
 * @property string $EventLocation
 * @property string $EventLatitude
 * @property string $EventLongtitude
 * @property integer $ClientId
 * @property date $EventStartDate
 * @property integer $EventEndDate
 * @property integer $IsFromTemplate
 * @property integer $IsDuplicated
 * @property integer $DuplicatedFromProject
 * @property integer $CreatedOn
 * @property integer $IsActive
 */
class ProjectPage extends ActiveRecord
{
    //const STATUS_DELETED = '0';
    //const STATUS_ACTIVE = '10';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%projectpages}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }
	
	/**
     * @return mix
     */
    public function getPages($ProjectId)
    {
		return Page::find()->where(['ProjectId' => $ProjectId])->all();
    }
	
	/**
     * @return mix
     */
    public function getRoles($ProjectId)
    {
		return Role::find()->where(['ProjectId' => $ProjectId])->all();
    }
	
	/**
     * @return mix
     */
    public function getClient($clientId)
    {
		return Client::find()->where(['clientId' => $clientId])->one();
    }
	
	/**
     * @return mix
     */
    public function getContact($ContactId)
    {
		return Contact::find()->where(['ContactId' => $ContactId])->one();
    }
}
