<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 24.11.2020
 * Time: 20:54
 */

namespace lib;


class Api
{
    protected $token;

    public $text_handlers = [];
    public $callback_handlers = [];
    public $photo_handlers = [];

    function __construct($token)
    {
        $this->token = $token;
    }

    protected function request($method, $data)
    {
        $url = "https://api.telegram.org/bot" . $this->token . '/' . $method;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        if (curl_error($ch)) {
            var_dump(curl_error($ch));
        } else {
            return json_decode($res);
        }
        return json_decode($res);
    }


    public function answer_callback_query($query_id, $text = null, $show_alert = false, $url = null)
    {
        $data = [
            'callback_query_id' => $query_id,
            'text' => $text,
            'show_alert' => $show_alert
        ];
        if ($url) $data['url'] = $url;
        return $this->request('answerCallbackQuery', $data);
    }

    public function delete_message($chat_id, $message_id)
    {
        $data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ];
        return $this->request('deleteMessage', $data);
    }


    public function send_message(
        $chat_id,
        $text,
        $reply_markup = null,
        $parse_mode = null)
    {
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup];
        return $this->request('sendMessage', $data);
    }

    public function send_photo(
        $chat_id,
        $photo,
        $caption = '',
        $reply_markup = null,
        $parse_mode = null)
    {
        $data = [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup];
        return $this->request('sendPhoto', $data);
    }

    public function send_document(
        $chat_id,
        $document,
        $caption = '',
        $reply_markup = null,
        $parse_mode = null)
    {
        $data = [
            'chat_id' => $chat_id,
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup];
        return $this->request('sendDocument', $data);
    }


    public function edit_message_text(
        $chat_id,
        $message_id,
        $text,
        $reply_markup = null,
        $parse_mode = null)
    {
        $data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup];
        return $this->request('editMessageText', $data);
    }

    public function edit_message_reply_markup(
        $chat_id,
        $message_id,
        $reply_markup = null)
    {
        $data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => $reply_markup];
        return $this->request('editMessagereplymarkup', $data);
    }

    function get_chat_member($chat_id,$user_id){
        $data = [
            'chat_id'=>$chat_id,
            'user_id'=>$user_id
        ];
        return $this->request('getChatmember',$data);
    }


    function keyboard($buttons, $size = 1)
    {
        $keyboard = [
            'keyboard' => [],
            'one_time_keyboard' => true,
            'resize_keyboard' => true];
        foreach ($buttons as $button) {
            $keyboard['keyboard'][] = ['text' => $button];
        }
        $keyboard['keyboard'] = array_chunk($keyboard['keyboard'], $size);
        return json_encode($keyboard);
    }

    function callback_keyboard($texts, $callback_data, $size = 1)
    {
        $keyboard = [
            'inline_keyboard' => []];
        foreach ($texts as $key => $value) {
            $keyboard['inline_keyboard'][] = ['text' => $value, 'callback_data' => $callback_data[$key]];
        }
        $keyboard['inline_keyboard'] = array_chunk($keyboard['inline_keyboard'], $size);
        return json_encode($keyboard);
    }

    /**
     * add text handler
     * usage:
     *  $bot->onText($regex,Handler $handler,$state);
     *
     * @param $regex string
     * @param $handler
     * @param bool $state
     */
    function onText($regex, $handler, $state = false)
    {
        $this->text_handlers[] = ['regex' => $regex, 'handler' => $handler, 'state' => $state];
    }

    function onPhoto($handler, $state = false)
    {
        $this->photo_handlers[] = ['handler' => $handler, 'state' => $state];
    }

    function onCallback($regex, $handler, $state = false)
    {
        $this->callback_handlers[] = ['regex' => $regex, 'handler' => $handler, 'state' => $state];
    }


    function webhook()
    {
        $update = json_decode(file_get_contents('php://input'), true);
        if (isset($update['message'])) {

            //handling text messages
            if (isset($update['message']['text'])) {
                $text = $update['message']['text'];
                foreach ($this->text_handlers as $handler) {
                    /**
                     * @var TextHandler $handle
                     */
                    if (preg_match("#" . $handler['regex'] . "#", $text)) {
                        if ($handler['state']) {
                            $state = User::get_state_byId($update['message']['chat']['id']);
                            if (preg_match("#" . $handler['state'] . "#", $state)) {
                                $handle = new $handler['handler']($this);
                                $handle->process($update['message'], $state);
                            } else {
                                continue;
                            }
                        } else {
                            $handle = new $handler['handler']($this);
                            $handle->process($update['message']);
                        }
                    }
                }
            }

            //handling photo messages
            if (isset($update['message']['photo'])) {
                $photo = $update['message']['photo'];
                foreach ($this->photo_handlers as $handler) {
                    /**
                     * @var PhotoHandler $handle
                     */
                    if ($handler['state']) {
                        $state = User::get_state_byId($update['message']['chat']['id']);
                        if (preg_match("#" . $handler['state'] . "#", $state)) {
                            $handle = new $handler['handler']($this);
                            $handle->process($update['message']);
                        } else {
                            continue;
                        }
                    } else {
                        $handle = new $handler['handler']($this);
                        $handle->process($update['message']);
                    }

                }
            }

        }


        if (isset($update['callback_query'])) {
            $data = $update['callback_query']['data'];
            foreach ($this->callback_handlers as $handler) {
                /**
                 * @var CallbackHandler $handle
                 */
                if (preg_match("#" . $handler['regex'] . "#", $data)) {
                    if ($handler['state']) {
                        $state = User::get_state_byId($update['callback_query']['from']['id']);
                        if (preg_match("#" . $handler['state'] . "#", $state)) {
                            $handle = new $handler['handler']($this);
                            $handle->process($update['callback_query']);
                        } else {
                            continue;
                        }
                    } else {
                        $handle = new $handler['handler']($this);
                        $handle->process($update['callback_query']);
                    }
                }
            }

        }

    }

}