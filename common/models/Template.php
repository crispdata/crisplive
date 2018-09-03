<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Template model
 *
 * @property integer $TemplateId
 * @property string $TemplateName
 * @property datetime $createdOn
 * @property integer $isActive
 */
class Template extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tbltemplates}}';
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
    public function getPages($TemplateId)
    {
		return TemplatePage::find()->where(['TemplateId' => $TemplateId])->all();
    }
	
	/**
     * @return mix
     */
    public function getRoles($TemplateId)
    {
		return TemplateRole::find()->where(['TemplateId' => $TemplateId])->all();
    }
	
	/**
     * @return mix
     */
    public function getContact($ContactId)
    {
		return User::find()->where(['UserId' => $ContactId])->one();
    }
}