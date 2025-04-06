<?php

use Rybel\backbone\Helper;

class BetHelper extends Helper
{

    public function createBet($data)
    {
        if ($data['type'] == "Quick Pick") {
            // Nothing to do, pass through
        } elseif ($data['type'] == 'Over/Under') {
            $data['a_title'] = "Over";
            $data['b_title'] = "Under";
            $data['c_title'] = null;
            $data['c_odds'] = null;
            $data['d_title'] = null;
            $data['d_odds'] = null;
        } else {
            $data['a_title'] = "Hit";
            $data['b_title'] = null;
            $data['b_odds'] = null;
            $data['c_title'] = null;
            $data['c_odds'] = null;
            $data['d_title'] = null;
            $data['d_odds'] = null;
        }

        $data['closes'] = null;
        if (!empty($data['closing'])) {
            $data['closes'] = '2025-04-12T' . $data['closing'];
        }

        return $this->query("INSERT INTO Bets (`title`, `type`, `description`, `a_title`, `a_odds`, `b_title`, `b_odds`, `c_title`, `c_odds`, `d_title`, `d_odds`, `closes`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $data['title'], $data['type'], $data['description'], $data['a_title'], $data['a_odds'], $data['b_title'], $data['b_odds'], $data['c_title'], $data['c_odds'], $data['d_title'], $data['d_odds'], $data['closes']);
    }

    public function updateBet($id, $data, $userHelper)
    {
        if ($data['type'] == "Quick Pick") {
            // Nothing to do, pass through
        } elseif ($data['type'] == 'Over/Under') {
            $data['a_title'] = "Over";
            $data['b_title'] = "Under";
            $data['c_title'] = null;
            $data['c_odds'] = null;
            $data['d_title'] = null;
            $data['d_odds'] = null;
        } else {
            $data['a_title'] = "Hit";
            $data['b_title'] = null;
            $data['b_odds'] = null;
            $data['c_title'] = null;
            $data['c_odds'] = null;
            $data['d_title'] = null;
            $data['d_odds'] = null;
        }

        $data['closes'] = null;
        if (!empty($data['closing'])) {
            $data['closes'] = '2025-04-12T' . $data['closing'];
        }

        if ($data['winner'] == '') {
            $data['winner'] = null;
        } else {
            $wagers = $this->getWagersForBet($id);
            foreach ($wagers as $wager) {
                if ($wager['selection'] == $data['winner']) {
                    if (!$this->setWagerOutcome($wager['id'], 'Winner')) {
                        return false;
                    }
                    $payout = $this->calculatePayout($wager['odds'], $wager['amount']);
                    if (!$userHelper->addTransactionForUser($wager['user_id'], $payout, 'Winning payout from bet: ' . $data['title'])) {
                        return false;
                    }
                } else if ($data['winner'] == 'Void') {
                    if (!$this->setWagerOutcome($wager['id'], 'Void')) {
                        return false;
                    }
                    if (!$userHelper->addTransactionForUser($wager['user_id'], $wager['amount'], 'Refund from voided bet: ' . $data['title'])) {
                        return false;
                    }
                } else {
                    if (!$this->setWagerOutcome($wager['id'], 'Loser')) {
                        return false;
                    }
                }
            }
        }

        return $this->query("UPDATE Bets SET `description` = ?, `a_odds` = ?, `b_odds` = ?, `c_odds` = ?, `d_odds` = ?, `closes` = ?, `winner` = ? WHERE id = ?", $data['description'], $data['a_odds'], $data['b_odds'], $data['c_odds'], $data['d_odds'], $data['closes'], $data['winner'], $id);
    }

    public function createWager($data, $user_id, $userHelper) {
        $bet = $this->getBetById($data['bet_id']);
        
        $data['user_id'] = $user_id;
        if (empty($data['selection'])) {
            $data['selection'] = 'a';
        }
        $data['odds'] = $bet[$data['selection'] . '_odds'];
        $data['selection'] = $bet[$data['selection'] . '_title'];

        if (!$userHelper->addTransactionForUser($data['user_id'], -1 * $data['amount'], 'Wager placed on ' . $data['selection'])) {
            return false;
        }

        return $this->query("INSERT INTO Wagers (`user_id`, `bet_id`, `selection`, `odds`, `amount`) VALUES (?, ?, ?, ?, ?)", $data['user_id'], $data['bet_id'], $data['selection'], $data['odds'], $data['amount']);
    }

    public function getBetById($id) {
        return $this->query("SELECT * FROM Bets WHERE id = ? LIMIT 1", $id);
    }

    public function getAllBets() {
        return $this->query("SELECT * FROM Bets");
    }

    public function getAllWagers() {
        return $this->query("SELECT * FROM Wagers");
    }

    public function getWagerById($id) {
        return $this->query("SELECT * FROM Wagers WHERE id = ? LIMIT 1", $id);
    }

    public function setWagerOutcome($id, $outcome) {
        return $this->query("UPDATE Wagers SET outcome = ? WHERE id = ?", $outcome, $id);
    }

    public function getWagersForBet($bet_id) {
        return $this->query("SELECT Wagers.id, Wagers.user_id, Wagers.outcome, Wagers.selection, Wagers.amount, Wagers.odds, Wagers.timestamp, Bets.title, Bets.description, Bets.type FROM Wagers, Bets WHERE Bets.id = ? AND Bets.id = Wagers.bet_id", $bet_id);
    }

    public function getWagersForUser($user_id) {
        return $this->query("SELECT Wagers.outcome, Wagers.selection, Wagers.amount, Bets.title, Bets.description, Bets.type, Wagers.odds, Wagers.timestamp FROM Wagers, Bets WHERE user_id = ? AND Bets.id = Wagers.bet_id", $user_id);
    }

    public function getWagersForUserWithOutcome($user_id, $outcome) {
        return $this->query("SELECT Wagers.outcome, Wagers.selection, Wagers.amount, Bets.title, Bets.description, Bets.type, Wagers.odds, Wagers.timestamp FROM Wagers, Bets WHERE user_id = ? AND Bets.id = Wagers.bet_id AND Wagers.outcome = ?", $user_id, $outcome);
    }

    public function getAllAvailableBetsForUser($user_id) {
        return $this->query("SELECT Bets.* FROM Bets WHERE NOT EXISTS (SELECT 1 FROM Wagers WHERE Wagers.bet_id = Bets.id AND Wagers.user_id = ?) AND (closes IS NULL OR Bets.closes > NOW()) AND winner IS NULL ORDER BY closes", $user_id);
    } 

    public function calculatePayout($odds, $amount) {
        if (substr($odds, 0, 1) == "+") {
            // Underdog
            return round(((substr($odds, 1)/100) + 1) * $amount, 2);
        } else {
            // Favorite
            return round(((100/substr($odds, 1)) + 1) * $amount, 2);
        }
    }
}
