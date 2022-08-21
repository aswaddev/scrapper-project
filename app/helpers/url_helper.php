<?php
// Simple page redirect
function redirect($page)
{
    header('LOCATION: ' . URLROOT . '/' . $page);
}
