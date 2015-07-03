<?php

namespace Psecio\Gatekeeper;

class SecurityQuestionCollectionTest extends \Psecio\Gatekeeper\Base
{
    /**
     * Test the location of security questions of a user by ID
     */
    public function testFindQuestionsByUserId()
    {
        $userId = 1;
        $return = [
            ['question' => 'Arthur', 'answer' => 'Dent', 'user_id' => $userId],
            ['name' => 'Ford', 'description' => 'Prefect', 'user_id' => $userId]
        ];

        $ds = $this->buildMock($return, 'fetch');
        $questions = new SecurityQuestionCollection($ds);

        $questions->findByUserId($userId);
        $this->assertCount(2, $questions);
    }
}