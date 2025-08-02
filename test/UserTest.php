<?php
require_once __DIR__ . '/autoload.php';

use PHPUnit\Framework\TestCase;
use agileFW\app\data\model\User;

/**
 * ユーザモデルのテストクラス
 * ユーザモデルの基本的な機能をテストする。
 */
class UserTest extends TestCase {
    /**
     * ユーザモデルの新規作成テスト
     */
    public function testNewUser() {
        $user = User::new();
        $this->assertTrue($user instanceof User, 'User should be an instance of User');
        $this->assertTrue($user->getPkey() < 0, 'New user should have a negative primary key');
    }

    /**
     * ユーザモデルの主キー取得テスト
     */
    public function testGetPkey() {
        $user = User::new(1);
        $this->assertTrue($user->getPkey() === 1, 'User primary key should be 1');
    }
    public function testGetLogic() {
        $user = User::new();
        $logic = $user->getLogicBase();
        $this->assertTrue($logic instanceof \agileFW\app\logic\UserLogic, 'Logic should be an instance of UserLogic');
    }
}
