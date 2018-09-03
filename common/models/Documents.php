<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Contact model
 *
 * @property integer $ContactId
 * @property string $FirstName
 * @property string $LastName
 * @property string $Street
 * @property string $City
 * @property string $State
 * @property string $Zip
 * @property string $Website
 * @property string $Mobile
 * @property string $ContactOffice
 * @property string $Fax
 * @property integer $TotalProjects
 * @property datetime $createdOn
 * @property integer $isActive
 */
class Documents extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%documents}}';
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