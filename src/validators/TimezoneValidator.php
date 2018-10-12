<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace yii\validators;

use Exception;
use Throwable;
use DateTimeZone;

/**
 * The validator checks if the attribute value is a valid timezone identifier according to the `timezone_identifiers_list` PHP function.
 *
 *
 * @author Dmitry Kuts <me@exieed.com>
 * @since  3.0.0
 */
class TimezoneValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute): void
    {
        $value  = $model->$attribute;
        $result = $this->validateValue($value);
        if ( ! empty($result)) {
            $this->addError($model, $attribute, $result[ 0 ], $result[ 1 ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value): array
    {
        try {
            new DateTimeZone($value);
        } catch (Exception $e) {
            return [$e->getMessage(), []];
        } catch (Throwable $e) {
            return [$e->getMessage(), []];
        }

        return null;
    }

}
