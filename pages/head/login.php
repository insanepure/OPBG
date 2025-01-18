<?php
if (isset($_GET['a']))
{
    $user_ip = $account->GetRealIP();
    $a = $_GET['a'];
    if($a == 'login' && isset($_POST['name']) && isset($safedPW))
    {
        if($userLoginActive)
        {
            $stayLogged = isset($_POST['staylogged']);
            $result = $account->LoginSafe($_POST['name'], $safedPW, $stayLogged);
            if (!$result)
            {
                $message = 'Der Account oder das Passwort ist falsch.';
            }
            else
            {
                $date_of_expiry = time() + 60 * 60 * 24 * 30;
                if(isset($_POST['personalizedAds']))
                {
                    setcookie( "personalizedAds",  1, $date_of_expiry);
                }
                else
                {
                    $date_of_expiry = time() - 10;
                    setcookie( "personalizedAds",  "", $date_of_expiry);
                }

                if(isset($_POST['userTracking']))
                {
                    setcookie( "userTracking",  1, $date_of_expiry);
                }
                else
                {
                    $date_of_expiry = time() - 10;
                    setcookie( "userTracking",  "", $date_of_expiry);
                }

                header('Location: index.php');
            }
        }
        else
        {
            $message = 'Der Login ist zurzeit deaktiviert.';
        }
    }
    else if($a == 'charlogout')
    {
        $player->Logout();
        $date_of_expiry = time() - 10;
        setcookie("ocharaid", "", $date_of_expiry);
        header('Location: ?p=charalogin');
        exit();
    }
    else if($a == 'userlogout')
    {
        $account->Logout();
        header('Location: index.php');
        exit();
    }
}

if ($account->IsLogged())
{
    header('Location: index.php');
    exit();
}
?>