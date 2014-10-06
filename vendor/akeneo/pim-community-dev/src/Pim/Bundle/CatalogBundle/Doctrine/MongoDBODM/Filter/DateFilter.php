<?php

namespace Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\Filter;

use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Pim\Bundle\CatalogBundle\Model\AbstractAttribute;
use Pim\Bundle\CatalogBundle\Doctrine\AttributeFilterInterface;
use Pim\Bundle\CatalogBundle\Doctrine\FieldFilterInterface;
use Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\ProductQueryUtility;
use Pim\Bundle\CatalogBundle\Context\CatalogContext;

/**
 * Date filter
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DateFilter implements AttributeFilterInterface, FieldFilterInterface
{
    /** @var QueryBuilder */
    protected $qb;

    /** @var CatalogContext */
    protected $context;

    /**
     * @param QueryBuilder   $qb
     * @param CatalogContext $context
     */
    public function __construct(QueryBuilder $qb, CatalogContext $context)
    {
        $this->qb      = $qb;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeFilter(AbstractAttribute $attribute, $operator, $value)
    {
        $field = ProductQueryUtility::getNormalizedValueFieldFromAttribute($attribute, $this->context);
        $this->addFieldFilter($field, $operator, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldFilter($field, $operator, $value)
    {
        $field = sprintf('%s.%s', ProductQueryUtility::NORMALIZED_FIELD, $field);

        switch ($operator) {
            case 'BETWEEN':
                $this->qb->field($field)->gt($this->getTimestamp($value[0]));
                $this->qb->field($field)->lt($this->getTimestamp($value[1]));
                break;

            case '>':
                $this->qb->field($field)->gt($this->getTimestamp($value));
                break;

            case '<':
                $this->qb->field($field)->lt($this->getTimestamp($value));
                break;

            case '=':
                $this->qb->field($field)->equals($this->getTimestamp($value));
                break;

            default:
                $this->qb->addAnd(
                    $this->qb->expr()
                        ->addOr($this->qb->expr()->field($field)->lt($this->getTimestamp($value['from'])))
                        ->addOr($this->qb->expr()->field($field)->gt($this->getTimestamp($value['to'])))
                );
        }

        return $this;
    }

    /**
     * Get timestamp from data
     *
     * @param \DateTime|string $data
     *
     * @return integer
     */
    private function getTimestamp($data)
    {
        return $data instanceof \DateTime ? $data->getTimestamp() : strtotime($data);
    }
}
