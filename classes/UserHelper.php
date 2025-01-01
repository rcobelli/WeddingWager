<?php

use Rybel\backbone\Helper;

class UserHelper extends Helper
{

    private const INITIAL_BALANCE = 500;

    public function getUser($name) {
        $response = $this->query("SELECT `id`, `admin` FROM Users WHERE `name` = ? LIMIT 1", $name);
        if ($response) {
            return $response;
        } else {
            return false;
        }
    }

    public function loginUser($name)
    {
        $existingUser = $this->getUser($name);
        if ($existingUser !== false) {
            return $existingUser;
        }

        if (!$this->query("INSERT INTO Users (name) VALUES (?)", $name)) {
            return false;
        }

        $user_id = $this->getLastInsertID();

        if (!$this->addTransactionForUser($user_id, self::INITIAL_BALANCE, "Initial balance")) {
            return false;
        }

        return ['id' => $user_id, 'admin' => false];
    }

    public function getLeaderboard()
    {
        return $this->query("SELECT `id`, `name`, `balance` FROM Users WHERE `admin` = 0 ORDER BY `balance` DESC");
    }

    public function getBalanceForUser($user_id)
    {
        return $this->query("SELECT `balance` FROM Users WHERE id = ? LIMIT 1", $user_id)['balance'];
    }

    public function addTransactionForUser($user_id, $delta, $memo) {
        echo $delta;
        if (!$this->query("INSERT INTO `Transactions` (user_id, delta, memo) VALUES (?, ?, ?)", $user_id, $delta, $memo)) {
            return false;
        }

        return $this->updateBalanceForUser($user_id);
    }

    public function getTransactionsForUser($user_id) {
        return $this->query("SELECT `delta`, `memo` FROM Transactions WHERE user_id = ?", $user_id);
    }

    private function updateBalanceForUser($user_id) {
        $transactions = $this->getTransactionsForUser($user_id);

        $balance = 0;
        foreach ($transactions as $trans) {
            $balance += $trans['delta'];
        }

        return $this->query('UPDATE Users SET balance = ? WHERE id = ?', $balance, $user_id);
    }
}
