<?php
namespace XoopsModules\Eric_signup;

use XoopsModules\Eric_signup\Eric_signup_data;
use XoopsModules\Tadtools\SimpleRest;

require dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

class Eric_signup_api extends SimpleRest
{
    public $uid    = '';
    public $user   = [];
    public $groups = [];
    private $token = '';

    public function __construct($token = '')
    {
        $this->token = $token;
        if (!isset($_SESSION['api_mode'])) {
            $_SESSION['api_mode'] = true;
        }

        if ($this->token) {
            $User         = $this->getXoopsSUser($this->token);
            $this->uid    = (int) $User['uid'];
            $this->groups = $User['groups'];
            $this->user   = $User['user'];

            //判斷是否對該模組有管理權限 $_SESSION['eric_signup_adm']
            if (!isset($this->user['eric_signup_adm'])) {
                $this->user['eric_signup_adm'] = $_SESSION['eric_signup_adm'] = ($this->uid) ? $this->isAdmin('eric_signup') : false;
            }

            // 判斷有無XXX的權限
            // if (!isset($this->user['權限名'])) {
            //     $_SESSION['權限名'] = $this->user['權限名'] = $this->powerChk('eric_signup', 權限編號);
            // }

        }
    }

    // 傳回目前使用者資訊
    public function user()
    {
        $data = ['uid' => (int) $this->uid, 'groups' => $this->groups, 'user' => $this->user];
        return $this->encodeJson($data);
    }

    // 轉成 json
    private function encodeJson($responseData)
    {
        if (empty($responseData)) {
            $statusCode   = 404;
            $responseData = array('error' => '無資料');
        } else {
            $statusCode = 200;
        }
        $this->setHttpHeaders($statusCode);

        $jsonResponse = json_encode($responseData, 256);
        return $jsonResponse;
    }

    // 傳回目前使用者資訊
    public function eric_signup_actions_index($only_enable = true)
    {
        $actions = Eric_signup_actions::get_all($only_enable);

        return $this->encodeJson($actions);
    }

    // 傳回目前活動報名者資料
    public function eric_signup_data_index($action_id)
    {
        // token是老的api的方法屬性，有token才掀資料
        $data = $this->token ? Eric_signup_data::get_all($action_id) : [];

        return $this->encodeJson($data);
    }
}
