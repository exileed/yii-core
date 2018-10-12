<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace yii\validators;

use yii\helpers\Yii;

/**
 * JsonValidator validates that the attribute value is a valid JSON string.
 *
 * @author Dmitriy Kuts <me@exileed.com>
 * @since  3.0.0
 */
class JsonValidator extends Validator
{

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} must be valid JSON string.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value): ?array
    {
        if ( ! is_scalar($value) && ! method_exists($value, '__toString')) {
            return null;
        }
        json_decode($value);
        return (json_last_error() === JSON_ERROR_NONE) ? null : [$this->message, []];
    }
}
