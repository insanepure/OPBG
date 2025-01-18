<?php
    if($database == NULL)
    {
        print 'This File ('.__FILE__.') should be after Database!';
    }

    include_once 'news.php';

    class NewsManager
    {
        private $database;
        private $news;

        function __construct($db, $num)
        {
            $this->database = $db;
            $this->news = array();
            $this->LoadNews($num);
        }

        public function GetNewsByID($id)
        {
            $newsCount = $this->GetNewsCount();
            $currentNews = null;
            for ($i = 0; $i < $newsCount; $i++)
            {
                $news = $this->GetNews($i);
                if($news->GetID() == $id)
                {
                    $currentNews = $news;
                    break;
                }
            }
            return $currentNews;
        }

        public function Post($player, $id, $text)
        {
            $currentNews = $this->GetNewsByID($id);
            if($currentNews == null)
                return 0;

            if(!preg_match("/^[A-Za-z0-9,.+\-!:;()_\s?äöüßÄÖÜ]+$/", $text))
            {
                return -1;
            }

            $comment[0] = $player->GetName();
            $comment[1] = $player->GetID();
            $comment[2] = $this->database->EscapeString($text);
            $currentNews->AddComment($comment);

            $commentsString = $currentNews->GetCommentsString();
            $this->database->Update('kommentare="'.$commentsString.'",likes="'.$currentNews->GetLikesString().'", dislikes="'.$currentNews->GetDisLikesString().'"','News','id = '.$id.'',1);

            return 1;
        }

        public function RemoveComment($id, $index)
        {
            $news = $this->GetNewsByID($id);
            $comments = explode('@', $news->GetCommentsString());
            array_splice($comments, $index, 1);
            $result = $this->database->Update('kommentare="'.implode('@', $comments).'"', 'News', 'id='.$id, 1);
            return $result;
        }

        public function Like($player, $id)
        {
            $currentNews = $this->GetNewsByID($id);
            if($currentNews == null)
            {
                return 0;
            }

            $currentNews->RemoveLike($player);
            $currentNews->RemoveDisLike($player);
            $currentNews->AddLike($player);
            $this->database->Update('likes="'.$currentNews->GetLikesString().'", dislikes="'.$currentNews->GetDisLikesString().'"','News','id = '.$id.'',1);
        }

        public function DisLike($player, $id)
        {
            $currentNews = $this->GetNewsByID($id);
            if($currentNews == null)
                return 0;

            $currentNews->RemoveLike($player);
            $currentNews->RemoveDisLike($player);
            $currentNews->AddDisLike($player);
            $this->database->Update('likes="'.$currentNews->GetLikesString().'", dislikes="'.$currentNews->GetDisLikesString().'"','News','id = '.$id.'',1);
        }

        public function RemoveLikes($player, $id)
        {
            $currentNews = $this->GetNewsByID($id);
            if($currentNews == null)
                return 0;

            $currentNews->RemoveLike($player);
            $currentNews->RemoveDisLike($player);
            $this->database->Update('likes="'.$currentNews->GetLikesString().'", dislikes="'.$currentNews->GetDisLikesString().'"','News','id = '.$id.'',1);
        }

        private function LoadNews($num)
        {
            $result = $this->database->Select('*','news','',$num,'id','DESC');
            if ($result)
            {
                if ($result->num_rows > 0)
                {
                    while($row = $result->fetch_assoc())
                    {
                        $news = new News($row);
                        array_push($this->news, $news);
                    }
                }
                $result->close();

            }
        }

        public function GetNewsCount()
        {
            return count($this->news);
        }

        public function &GetNews(int $index)
        {
            return $this->news[$index];
        }
    }
