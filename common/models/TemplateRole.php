<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Template Role model
 *
 * @property integer $PageID
 * @property integer $ProjectId
 * @property string $PageName
 * @property datetime $createdOn
 * @property integer $isActive
 */
class TemplateRole extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tbltemplateroles}}';
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
}