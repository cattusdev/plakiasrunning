<?php
    
if ($mainUser->logout()) header("Location: /", true, 302);