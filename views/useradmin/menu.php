<div id="navigation">

    <ul class="menu">
        <?php
        if (Auth::instance()->logged_in())
        {
            if( Auth::instance()->logged_in('admin') )
            {
                echo '<li>'.Html::anchor('admin_user', __('user.admin')).'</li>';
            }
            echo '<li>'.Html::anchor('user/profile', __('my.profile')).'</li>';
            echo '<li>'.Html::anchor('user/logout', __('logout')).'</li>';
        }
        else
        {
            echo '<li>'.Html::anchor('user/register', __('register')).'</li>';
            echo '<li>'.Html::anchor('user/login', __('login')).'</li>';
        }
        ?>
    </ul>
    
</div>