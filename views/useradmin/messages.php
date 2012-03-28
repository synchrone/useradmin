<?php

// output messages
if( Message::count() > 0 )
{
    echo '<div class="block">';
    echo '<div class="content" style="padding: 10px 15px;">';
    echo Message::output();
    echo '</div></div>';
}