<Button type="button" onclick="OpenPopupPage('Teilnehmen','clan/challenged.php)">Teilnehmen</Button>
<br /><br />
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'classes/player/player.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
include_once $_SERVER['DOCUMENT_ROOT'].'classes/story/story.php';


$SearchStory = $database->Select('*', 'story', '', 999999);
if($SearchStory) {
    while ($story = $SearchStory->fetch_assoc()) {
        if ($story['npcs'] != '') {
            $test = explode(';', $story['supportnpcs']);
            foreach ($test as $leckmich) {

                $npc = new NPC($database, $leckmich);

            }
        }
    }
}
