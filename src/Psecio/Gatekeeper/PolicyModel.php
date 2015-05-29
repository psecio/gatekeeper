<?php

namespace Psecio\Gatekeeper;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class PolicyModel extends \Psecio\Gatekeeper\Model\Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected $tableName = 'policies';

    /**
     * Model properties
     * @var array
     */
    protected $properties = array(
    	'id' => array(
            'description' => 'User ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'expression' => array(
        	'description' => 'Policy Expression',
        	'column' => 'expression',
        	'type' => 'string'
        ),
        'description' => array(
        	'description' => 'Policy Description',
        	'column' => 'description',
        	'type' => 'string'
        ),
        'name' => array(
        	'description' => 'Policy Name',
        	'column' => 'name',
        	'type' => 'string'
        )
    );

    public function evaluate($data, $expression = null)
    {
    	if ($this->id === null) {
    		throw new \InvalidArgumentException('Policy not loaded!');
    	}
        $expression = ($expression === null) ? $this->expression : $expression;
        if (!is_array($data)) {
            $data = array($data);
        }
        $context = array();
        foreach ($data as $index => $item) {
            if (is_numeric($index)) {
                // Resolve it to a class name
                $ns = explode('\\', get_class($item));
                $index = str_replace('Model', '', array_pop($ns));
            }
            $context[strtolower($index)] = $item;
        }

        $language = new ExpressionLanguage();
        try {
            return $language->evaluate($expression, $context);
        } catch (\Exception $e) {
            throw new Exception\InvalidExpressionException($e->getMessage());
        }

    }
}