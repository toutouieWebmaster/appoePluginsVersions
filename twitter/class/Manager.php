<?php

namespace App\Plugin\Twitter;

class Manager
{
    private $connection = null;

    public function __construct()
    {
        if (is_null($this->connection) && twitter_is_active()) {
            $this->connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
        }
    }

    /**
     * @param $query
     * @return array|object
     */
    public function twitter_search_user($query)
    {

        return $this->connection->get('users/search', ['q' => $query]);
    }

    /**
     * @param $message
     * @return array|object
     */
    public function twitter_share_article($message)
    {
        return $this->connection->post('statuses/update', ['status' => $message]);
    }

    /**
     * @param string $listName
     * @return array|object
     */
    public function twitter_get_lists($listName = '')
    {

        return $this->connection->get('lists/list', ['screen_name' => TWITTER_USERNAME, 'Name' => $listName]);
    }

    /**
     * @param $listName
     * @return array|object
     */
    public function twitter_get_list_members($listName)
    {

        $listId = '';
        $list = $this->twitter_get_lists($listName);

        if (is_array($list)) {
            $listId = $list[0]->id;
        }

        return $this->connection->get('lists/members', ['screen_name' => TWITTER_USERNAME, 'list_id' => $listId]);
    }

    /**
     * @param $listName
     * @return array
     */
    public function twitter_get_ids_list_members($listName)
    {
        $usersIds = [];
        $usersList = $this->twitter_get_list_members($listName);

        foreach ($usersList->users as $key => $user) {
            $usersIds[$key] = $user->id_str;
        }

        return $usersIds;
    }

    /**
     * @param $userId
     * @param $message
     * @return array|object
     */
    public function twitter_send_directMessage($userId, $message)
    {

        $data = [
            'event' => [
                'screen_name' => TWITTER_USERNAME,
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $userId
                    ],
                    'message_data' => [
                        'text' => $message
                    ]
                ]
            ]
        ];
        return $this->connection->post('direct_messages/events/new', $data, true);
    }
}