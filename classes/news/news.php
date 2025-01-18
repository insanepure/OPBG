<?php
    class News
    {
        private $data;

        function __construct($initialData)
        {
            $this->data = $initialData;
        }

        public function GetID()
        {
            return $this->data['id'];
        }

        public function GetAuthor()
        {
            return $this->data['authorname'];
        }

        public function GetAuthorID()
        {
            return $this->data['author'];
        }

        public function GetAuthorImage()
        {
            return $this->data['authorimage'];
        }

        public function GetTitle()
        {
            return $this->data['title'];
        }

        public function GetComments()
        {
            if($this->data['kommentare'] == '')
            {
                return array();
            }
            else
            {
                return explode('@',$this->data['kommentare']);
            }
        }

        public function GetCommentsString()
        {
            return $this->data['kommentare'];
        }

        public function GetCommentCount()
        {
            return count($this->GetComments());
        }

        public function AddComment($comment)
        {
            $comments = $this->GetComments();
            array_push($comments, implode(';', $comment));
            $this->data['kommentare'] = htmlspecialchars(implode('@',$comments));
        }

        public function GetDate()
        {
            $dateStr = strtotime( $this->data['date'] );
            return date( 'd.m.Y H:i', $dateStr);
        }

        public function GetText()
        {
            return $this->data['text'];
        }

        public function GetLikes()
        {
            if($this->data['likes'] == '')
                return array();

            return explode(';',$this->data['likes']);
        }

        public function GetLikesString()
        {
            return $this->data['likes'] ;
        }

        public function GetDisLikes()
        {
            if($this->data['dislikes'] == '')
                return array();

            return explode(';',$this->data['dislikes']);
        }

        public function GetDisLikesString()
        {
            return $this->data['dislikes'] ;
        }

        public function GetLikeCount()
        {
            return count($this->GetLikes());
        }

        public function GetDisLikeCount()
        {
            return count($this->GetDisLikes());
        }

        public function HasLiked($player)
        {
            return in_array($player, $this->GetLikes());
        }

        public function HasDisLiked($player)
        {
            return in_array($player, $this->GetDisLikes());
        }

        public function AddLike($player)
        {
            $likes = $this->GetLikes();
            array_push($likes, $player);
            $this->data['likes'] = implode(';',$likes);
        }

        public function AddDisLike($player)
        {
            $dislikes = $this->GetDisLikes();
            array_push($dislikes, $player);
            $this->data['dislikes'] = implode(';',$dislikes);
        }

        public function RemoveLike($player)
        {
            $likes = $this->GetLikes();
            $key = array_search($player, $likes);
            if($key === false)
                return;

            array_splice($likes, $key, 1);
            $this->data['likes'] = implode(';',$likes);
        }

        public function RemoveDisLike($player)
        {
            $dislikes = $this->GetDisLikes();
            $key = array_search($player, $dislikes);
            if($key === false)
                return;

            array_splice($dislikes, $key, 1);
            $this->data['dislikes'] = implode(';',$dislikes);
        }

    }
