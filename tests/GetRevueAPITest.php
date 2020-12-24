<?php

use PHPUnit\Framework\TestCase;

class GetRevueApiTest extends TestCase
{

    /**
     * GETREVUE Class Object
     *
     * @var \Firewards\GetRevueApi
     */
    protected $api;

    /**
     * Test subscribed user email
     *
     * @var string
     */
    protected $testSubscribedEmail;

    /**
     * @var string
     */
    protected $testNewSubscriber;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        include_once(__DIR__ . "/config.php");

        $api_key = GETREVUE_API_KEY;
        $this->testSubscribedEmail = GETREVUE_TEST_EMAIL;
        $this->api = new \Firewards\GetRevueApi($api_key, true);

        $this->testNewSubscriber = 'TEST_' . rand(1000000,9999999) . '@firewards.com';
        //$this->testNewSubscriber = 'test_6357561@firewards.com';
    }

    private function _assertListResponse(array $list)
    {
        $this->assertEquals(
            array(
                'id' => 64471,
                'name' => 'Subscribers',
                'created_at' => '2020-08-16T16:35:58.013Z',
                'updated_at' => '2020-08-16T16:35:58.013Z',
                'account_id' => 64572,
                'verified' => false,
                'sync_settings' =>
                    array(
                        'ftp' =>
                            array(
                                'enabled' => false,
                                'interval' => 'day',
                                'to_email' => NULL,
                                'time_zone' => 'UTC',
                                'day_of_week' => 1,
                                'hour_of_day' => 10,
                                'minute_of_hour' => 0,
                            ),
                    ),
            ), $list);


        $this->assertArrayHasKey('id', $list);
        $this->assertArrayHasKey('name', $list);
        $this->assertArrayHasKey('created_at', $list);
        $this->assertArrayHasKey('account_id', $list);
        $this->assertArrayHasKey('verified', $list);
        $this->assertArrayHasKey('sync_settings', $list);
    }

    public function testAddSubscriber()
    {
        $subscriber = $this->api->addSubscriber($this->testNewSubscriber, 'firstName', 'lastName');

        $this->assertArrayHasKey('id', $subscriber);
        $this->assertArrayHasKey('list_id', $subscriber);
        $this->assertArrayHasKey('email', $subscriber);
        $this->assertArrayHasKey('first_name', $subscriber);
        $this->assertArrayHasKey('last_name', $subscriber);
        $this->assertArrayHasKey('last_changed', $subscriber);

        $this->assertEquals('firstName', $subscriber['first_name']);
        $this->assertEquals('lastName', $subscriber['last_name']);

        // Sleep some time because revue needs the time for the subscriber to be availabale through the api.
        sleep(45);
    }

    public function testAddExistingSubscriber()
    {
        $subscriber = $this->api->addSubscriber($this->testSubscribedEmail, 'firstName', 'lastName');
        $this->assertEquals(false, $subscriber);
    }


    public function testUnsubscribeNonExistingSubscriber()
    {
        $unsub = $this->api->unsubscribe('jkbasdkjbsajkbdsajkbasdjkbsdbjkdsa@kjsadkjbsabdas.com');
        $this->assertEquals(false, $unsub);
    }

    public function testGetUnsubscribed()
    {
        $unsubs = $this->api->getUnsubscribed();
        $this->assertNotCount(0, $unsubs);
        $this->assertGreaterThan(0, $unsubs);
    }

    public function testGetAccountProfileUrl()
    {
        $account = $this->api->getAccountProfileUrl();
    }

    public function testGetSubscribers()
    {
        $subscribers = $this->api->getSubscribers();
        $this->assertNotCount(0, $subscribers);
        $this->assertGreaterThan(GETREVUE_TEST_LIST_MIN_SUBSCRIBER_COUNT, $subscribers);
    }

    public function testGetLists()
    {
        $lists = $this->api->getLists();

        $this->assertArrayHasKey(0, $lists);
        $this->_assertListResponse($lists[0]);
    }


    public function testGetList()
    {
        $list = $this->api->getList(GETREVUE_TEST_LIST_ID);
        $this->_assertListResponse($list);
    }

/*
    public function testUpdateSubscriber()
    {
        $subscriber = $this->api->updateSubscriber($this->testNewSubscriber, 'firstName2', 'lastName2');

        $this->assertArrayHasKey('id', $subscriber);
        $this->assertArrayHasKey('list_id', $subscriber);
        $this->assertArrayHasKey('email', $subscriber);
        $this->assertArrayHasKey('first_name', $subscriber);
        $this->assertArrayHasKey('last_name', $subscriber);
        $this->assertArrayHasKey('last_changed', $subscriber);

        $this->assertEquals('firstName2', $subscriber['first_name']);
        $this->assertEquals('lastName2', $subscriber['last_name']);
    }
*/

    public function testUnsubscribeSubscriber()
    {
        $unsub = $this->api->unsubscribe($this->testNewSubscriber);

        $this->assertArrayHasKey('id', $unsub);
        $this->assertArrayHasKey('list_id', $unsub);
        $this->assertArrayHasKey('email', $unsub);
        $this->assertArrayHasKey('first_name', $unsub);
        $this->assertArrayHasKey('last_name', $unsub);
        $this->assertArrayHasKey('last_changed', $unsub);
    }
}
