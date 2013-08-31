<?php

class Controller_admin extends Controller
{

    protected $requiredRole = Owaka::AUTH_ROLE_ADMIN;
    protected $requiredRole_nonadmin = Owaka::AUTH_ROLE_NONE;
}