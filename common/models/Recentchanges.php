<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Recentchanges model
 *
 * @property integer $RecentChangeId
 * @property integer $UserId
 * @property integer $ContactId
 * @property string $Note
 * @property datetime $createdOn
 * @property integer $IsDismiss
 * @property integer $isActive
 * @property integer $IsViewed
 */
class Recentchanges extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tblrecentchanges}}';
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