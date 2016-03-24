<?php

namespace RunetId\ApiClient;

use Ruvents\DataReconstructor\DataReconstructor;

/**
 * Class ModelReconstructor
 * @package RunetId\ApiClient
 */
class ModelReconstructor extends DataReconstructor
{
    /**
     * @inheritdoc
     */
    protected $options = [
        'map' => [
            'user' => [
                'CreationTime' => 'DateTime',
                'Photo' => 'user_photo',
                'Work' => 'user_work',
                'Status' => 'user_status',
            ],
            'user_work' => [
                'Company' => 'user_work_company',
            ],
            'user_status' => [
                'UpdateTime' => 'DateTime',
            ],
        ],
        'model_classes' => [
            'error' => 'RunetId\ApiClient\Model\Error',
            'user' => 'RunetId\ApiClient\Model\User',
            'user_work_company' => 'RunetId\ApiClient\Model\User\Company',
            'user_photo' => 'RunetId\ApiClient\Model\User\Photo',
            'user_status' => 'RunetId\ApiClient\Model\User\Status',
            'user_work' => 'RunetId\ApiClient\Model\User\Work',
            'prof_interest' => 'RunetId\ApiClient\Model\ProfInterest',
        ],
    ];

    /**
     * @inheritdoc
     */
    protected function reconstructObject($data, $className, array $map)
    {
        $realClassName = $this->replaceModelClass($className);

        switch ($className) {
            case 'DateTime':
                $object = new \DateTime($data);
                break;

            default:
                $object = parent::reconstructObject($data, $realClassName, $map);
        }

        return $object;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function replaceModelClass($name)
    {
        $cleanName = $name;
        $isArray = false;

        if (substr($name, -2) === '[]') {
            $cleanName = substr($name, 0, -2);
            $isArray = true;
        }

        if (isset($this->options['model_classes'][$cleanName])) {
            return $this->options['model_classes'][$name].($isArray ? '[]' : '');
        }

        return $name;
    }
}