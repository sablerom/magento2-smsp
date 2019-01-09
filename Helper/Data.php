<?php
namespace SableSoft\Smsp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Helper class Data
 *
 * @package SableSoft\Smsp\Helper
 */
class Data extends AbstractHelper {

    const TYPE_URL      = 'url';
    const TYPE_SEX      = 'sex';
    const TYPE_FLAG     = 'flag';
    const TYPE_SORT     = 'sort';
    const TYPE_EMAIL    = 'email';
    const TYPE_STRING   = 'string';
    const TYPE_STRINGS  = 'strings';
    const TYPE_NUMBER   = 'number';
    const TYPE_NUMBERS  = 'numbers';

    const TYPE_PHONE    = 'phone';
    const TYPE_PHONES   = 'phones';

    protected $sexTypes = [ 'M', 'F', 'N' ];
    protected $sortTypes = [ 'asc', 'desc' ];

    /**
     * Validate and prepare param value @todo
     *
     * @param string|array $value
     * @param string $type
     * @return bool
     */
    public function validate( &$value, string $type ) : bool {
        $result = true;
        switch( $type ) {
            case self::TYPE_URL:
                $value = filter_var( $value, FILTER_SANITIZE_URL );
                $result = filter_var( $value, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED );
                break;
            case self::TYPE_SEX:
                $result = in_array( $value, $this->sexTypes );
                break;
            case self::TYPE_FLAG:
                $value = ( (bool) $value ) ? 1 : 0;
                break;
            case self::TYPE_SORT:
                $result = in_array( $value, $this->sortTypes );
                break;
            case self::TYPE_EMAIL:
                $value = filter_var( $value, FILTER_SANITIZE_EMAIL );
                $result = filter_var( $value, FILTER_VALIDATE_EMAIL );
                break;
            case self::TYPE_STRING:
                $result = !empty( (string) $value );
                break;
            case self::TYPE_STRINGS:
                $array = [];
                foreach( (array) $value as $v )
                    if( $this->validate( $v, self::TYPE_STRING ) )
                        $array[] = $v;
                $value = implode(',', $array );
                $result = !empty( $value );
                break;
            case self::TYPE_PHONE:
            case self::TYPE_NUMBER:
                $result = is_numeric( $value );
                break;
            case self::TYPE_PHONES:
            case self::TYPE_NUMBERS:
                $array = [];
                foreach( (array) $value as $v )
                    if( $this->validate( $v, self::TYPE_NUMBER ) )
                        $array[] = $v;
                $value = implode(',', $array );
                $result = !empty( $value );
                break;
        }

        return $result;
    }
}
