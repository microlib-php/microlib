<?php
/**
 * Created by PhpStorm.
 * User: Kakashi
 * Date: 24.11.2020
 * Time: 20:10
 */

namespace lib;


class User
{

    public $id;
    public $username = Null;

    function __construct($id)
    {
        $this->id = $id;
    }


    /*
     * set_state sets users state in directory tmp/
     * call function without arguments to delete state of user
     */
    public function set_state($state = false)
    {
        if (is_dir('./state')) {
            if ($state) file_put_contents('./state/' . $this->id, $state);
            else unlink('./state/' . $this->id);
        } else {
            mkdir('./state');
            file_put_contents('./state/' . $this->id, $state);
        }
    }


    /*
     * get_state gets users state
     */
    public function get_state()
    {
        return file_get_contents('./state/' . $this->id);
    }

    /*
     * get_state_byId is used to get users state without creating object of class User
     */
    public static function get_state_byId($id)
    {
        return file_exists('./state/' . $id) ? file_get_contents('./state/' . $id) : null;
    }

    /*
     * set_state_byId is used to set users state without creating object of class User
     */
    public static function set_state_byId($id, $state = false)
    {
        if (is_dir('./state')) {
            if ($state) file_put_contents('./state/' . $id, $state);
            else unlink('./state/' . $id);
        } else {
            mkdir('./state');
            file_put_contents('./state/' . $id, $state);
        }
    }

}
