<?php
include_once 'classes/actions/actionmanager.php';
include_once 'classes/actions/action.php';

if($player->IsLogged() && $player->GetArank() == 3) {
    if(isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $otherPlayer = new Player($database, $_GET['id']);

        echo "<h1>" . $otherPlayer->GetName() . "</h1>";
        // die aktuellen Stats vom Spieler
        $verteilerstats = (($otherPlayer->GetMaxLP() / 10) + ($otherPlayer->GetMaxKP() / 10) + ($otherPlayer->GetAttack() / 2) + $otherPlayer->GetDefense() + $otherPlayer->GetStats());
        echo "Im Besitz von: " . $verteilerstats . " Stats<br /><hr><br />";

        // Stats aus Story
        $level = $otherPlayer->GetLevel() - 1;
        $storyStats = $level * 25;
        echo "Durch Story erhaltene Stats: " . $storyStats . "<br /><br />";

        // Stats aus Spezial Training
        $specialtrainings = explode(';', $otherPlayer->GetSpecialTrainings());
        $id = 0;
        $specialactionstats = 0;
        $ahours = 0;
        while ($specialtrainings[$id]) {
            $actions = $actionManager->GetAction($specialtrainings[$id]);
            if ($actions != NULL) {
                $specialactionstats += $actions->GetStats();
                $ahours += ($actions->GetMinutes() / 60);
            }
            $id++;
        }
        echo "Spieler hat durch Spezialtrainings " . $specialactionstats . " bekommen <br /><br />";
        //

        // Lernzeit für Aktionen
        $attacks = explode(";", $otherPlayer->GetAttacks());
        $attackTime = 0;
        foreach ($attacks as $attack) {
            $attackManager = new AttackManager($database);
            $atk = $attackManager->GetAttack($attack);
            if ($atk->GetLearnTime() < 60) {
                if ($atk->GetID() != 1 && $atk->GetID() != 2 && $atk->GetID() != 4) {
                    $attackTime += $atk->GetLearnTime();
                }
            }
        }

        // Stats aus Statkämpfen
        $isfights = floor($otherPlayer->GetTotalStatsFights() / pow(10, 1)) * pow(10, 1);
        echo $isfights . " Stats hat der Spieler durch Statfights bekommen <br /><br />";
        //

        // Stats durch Promocodes
        echo "Der Spieler hat durch Promocodes " . $otherPlayer->GetPromoStats($otherPlayer->GetID()) . " Stats erhalten <br /><br />";
        //

        // Stats durch Dungeons
        $dungeons = $otherPlayer->GetExtraDungeons();
        $dungeonStats = 0;
        if (!empty($dungeons)) {
            foreach ($dungeons as $dungeon) {
                $event = new Event($database, $dungeon[0]);
                if ($event->GetStats() != 0) {
                    $dungeonStats += ($event->GetStats() * $dungeon[1]);
                }
            }
        }
        //

        echo "Der Spieler hat durch Dungeons " . $dungeonStats . " Stats bekommen. <br /><br />";

        // Tatsächliche mögliche Stats
        $date1 = new DateTime('2022-05-26T18:00:00');
        $date2 = new DateTime();
        $date3 = new DateTime($otherPlayer->GetCreationTime());

        $diff = $date2->diff($date1);

        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);

        $hoursC = 0;
        if ($date1->getTimestamp() <= $date3->getTimestamp())
        {
            $diffC = $date3->diff($date1);
            $hoursC = $diffC->h;
        }

        $hours = $hours - ($hoursC + $ahours + 39 + $attackTime);

        $stats = ($hours * 6) + ($hoursC * 5);

        $statsAtStart = 400;


        echo "<br /><br /><br /><br />Der Spieler sollte maximal " . ($stats + $otherPlayer->GetPromoStats($otherPlayer->GetID()) + $specialactionstats + $storyStats + $isfights + $dungeonStats + $statsAtStart) . " Statpunkte haben. <br /><br />";
        echo $stats." Statpunkte durch Training <br /><br />";
        echo $otherPlayer->GetPromoStats($otherPlayer->GetID())." Statpunkte durch Promocodes <br /><br />";
        echo $specialactionstats . " Statpunkte durch Spezialtrainings. <br /><br />";
        echo $isfights . " Statpunkte durch Statfights. <br /><br />";
        echo $dungeonStats . " Statpunkte durch Dungeons. <br /><br />";
        echo $statsAtStart . " Statpunkte zum Start. <br /><br />";
        echo $storyStats . " Statpunkte durch Level <br /><br /><br /><br />";

        echo "Differenz: " . ($verteilerstats - ($stats + $otherPlayer->GetPromoStats($otherPlayer->GetID()) + $specialactionstats + $storyStats + $isfights + $dungeonStats + $statsAtStart));
    }
    else {
        $result = $database->Select('*', 'accounts', 'arank=0 and banned=0 and deleted=0');
        if($result)
        {
            while($row = $result->fetch_assoc())
            {
                $otherPlayer = new Player($database, $row['id'], $actionManager);

                // die aktuellen Stats vom Spieler
                $verteilerstats = (($otherPlayer->GetMaxLP() / 10) + ($otherPlayer->GetMaxKP() / 10) + ($otherPlayer->GetAttack() / 2) + $otherPlayer->GetDefense() + $otherPlayer->GetStats());

                // Stats aus Story
                $level = $otherPlayer->GetLevel() - 1;
                $storyStats = $level * 25;

                // Stats aus Spezial Training
                $specialtrainings = explode(';', $otherPlayer->GetSpecialTrainings());
                $id = 0;
                $specialactionstats = 0;
                $ahours = 0;
                while ($specialtrainings[$id]) {
                    $actions = $actionManager->GetAction($specialtrainings[$id]);
                    if ($actions != NULL) {
                        $specialactionstats += $actions->GetStats();
                        $ahours += ($actions->GetMinutes() / 60);
                    }
                    $id++;
                }

                // Lernzeit für Aktionen
                $attacks = explode(";", $otherPlayer->GetAttacks());
                $attackTime = 0;
                foreach ($attacks as $attack) {
                    $attackManager = new AttackManager($database);
                    $atk = $attackManager->GetAttack($attack);
                    if ($atk->GetLearnTime() < 60) {
                        if ($atk->GetID() != 1 && $atk->GetID() != 2 && $atk->GetID() != 4) {
                            $attackTime += $atk->GetLearnTime();
                        }
                    }
                }

                // Stats aus Statkämpfen
                $isfights = floor($otherPlayer->GetTotalStatsFights() / pow(10, 1)) * pow(10, 1);

                // Stats durch Dungeons
                $dungeons = $otherPlayer->GetExtraDungeons();
                $dungeonStats = 0;
                if (!empty($dungeons)) {
                    foreach ($dungeons as $dungeon) {
                        $event = new Event($database, $dungeon[0]);
                        if ($event->GetStats() != 0) {
                            $dungeonStats += ($event->GetStats() * $dungeon[1]);
                        }
                    }
                }
                //

                // Tatsächliche mögliche Stats
                $date1 = new DateTime('2022-05-26T18:00:00');
                $date2 = new DateTime();
                $date3 = new DateTime($otherPlayer->GetCreationTime());

                $diff = $date2->diff($date1);

                $hours = $diff->h;
                $hours = $hours + ($diff->days * 24);

                $hoursC = 0;
                if ($date1->getTimestamp() <= $date3->getTimestamp()) {
                    $diffC = $date3->diff($date1);
                    $hoursC = $diffC->h;
                }

                $hours = $hours - ($hoursC + $ahours + 39 + $attackTime);
                //echo $hours . " - " . $hoursC;

                $stats = ($hours * 6) + ($hoursC * 5);

                $statsAtStart = 400;

                $finalStats = ($verteilerstats - ($stats + $otherPlayer->GetPromoStats($otherPlayer->GetID()) + $specialactionstats + $storyStats + $isfights + $dungeonStats + $statsAtStart));

                if($finalStats > 0) {
                    //if($otherPlayer->GetCreationTime() > "2022-05-25 18:05:00") {
                        echo $hours . " - " . $hoursC . " - ";
                        echo $otherPlayer->GetCreationTime() . " - " . $otherPlayer->GetName() . " - Differenz: " . ($verteilerstats - ($stats + $otherPlayer->GetPromoStats($otherPlayer->GetID()) + $specialactionstats + $storyStats + $isfights + $dungeonStats + $statsAtStart)) . " <br />";
                        if (isset($_GET['a']) && $_GET['a'] == "reset") {
                            $otherPlayer->SetStats(($stats + $otherPlayer->GetPromoStats($otherPlayer->GetID()) + $specialactionstats + $storyStats + $isfights + $dungeonStats + $statsAtStart));
                            $otherPlayer->SetLP(100);
                            $otherPlayer->SetMaxLP(100);
                            $otherPlayer->SetKP(100);
                            $otherPlayer->SetMaxKP(100);
                            $otherPlayer->SetAttack(20);
                            $otherPlayer->SetDefense(10);
                            $otherPlayer->SetStatsResetted(1);
                            $otherPlayer->SetResettedStatsAmount(($stats + $otherPlayer->GetPromoStats($otherPlayer->GetID()) + $specialactionstats + $storyStats + $isfights + $dungeonStats + $statsAtStart));

                            if ($otherPlayer->GetAction() == 1) {
                                $statpoints = 0;
                                $action = $actionManager->GetAction($otherPlayer->GetAction());
                                $actionTimes = $otherPlayer->GetActionTime();
                                $leftMinutes = ceil($otherPlayer->GetActionCountdown() / 60);
                                $elapsedMinutes = $actionTimes - $leftMinutes;
                                $actionMinutes = $action->GetMinutes();
                                $times = floor($elapsedMinutes / $actionMinutes);
                                $statpoints = $times * $action->GetStats();
                                if ($statpoints > 0) {
                                    $otherPlayer->RefreshAction2();
                                }
                            }
                            if ($otherPlayer->GetAction() == 8) {
                                $statpoints = 0;
                                $action = $actionManager->GetAction($otherPlayer->GetAction());
                                $actionTimes = $otherPlayer->GetActionTime();
                                $leftMinutes = ceil($otherPlayer->GetActionCountdown() / 60);
                                $elapsedMinutes = $actionTimes - $leftMinutes;
                                $actionMinutes = $action->GetMinutes();
                                $times = floor($elapsedMinutes / $actionMinutes);
                                $statpoints = $times * $action->GetStats();
                                if ($statpoints > 0) {
                                    $otherPlayer->RefreshAction2();
                                }
                            }
                            if ($otherPlayer->GetAction() == 9) {
                                $statpoints = 0;
                                $action = $actionManager->GetAction($otherPlayer->GetAction());
                                $actionTimes = $otherPlayer->GetActionTime();
                                $leftMinutes = ceil($otherPlayer->GetActionCountdown() / 60);
                                $elapsedMinutes = $actionTimes - $leftMinutes;
                                $actionMinutes = $action->GetMinutes();
                                $times = floor($elapsedMinutes / $actionMinutes);
                                $statpoints = $times * $action->GetStats();

                                if ($statpoints > 0) {
                                    $otherPlayer->RefreshAction2();
                                    $otherPlayer->SetBerry($otherPlayer->GetBerry() + $times);
                                }
                            }
                            $database->Update('lp="100",kp="100",mlp="100",mkp="100",attack="20",defense="10",statsresetted=1,zeni=' . $otherPlayer->GetBerry() . ',actionstart="' . $otherPlayer->GetActionStart() . '",actiontime=' . $otherPlayer->GetActionTime() . ',stats=' . $otherPlayer->GetResettedStatsAmount() . ',resetamount=' . $otherPlayer->GetResettedStatsAmount(), 'accounts', 'id=' . $otherPlayer->GetID());
                            echo 'lp=100,kp=100,mlp=100,mkp=100,attack=20,defense=10,statsresetted=1,zeni=' . $otherPlayer->GetBerry() . ',actionstart="' . $otherPlayer->GetActionStart() . '"actiontime=' . $otherPlayer->GetActionTime() . ',stats=' . $otherPlayer->GetResettedStatsAmount() . ',resetamount=' . $otherPlayer->GetResettedStatsAmount() . '<br/><br/>';
                        //}
                    }
                }
            }
        }
    }
}