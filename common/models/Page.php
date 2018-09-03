<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Page model
 *
 * @property integer $PageID
 * @property integer $ProjectId
 * @property string $PageName
 * @property datetime $createdOn
 * @property integer $isActive
 */
class Page extends ActiveRecord
{
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
}