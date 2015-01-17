<?php

if( isset( $_POST['username'] ) && isset( $_POST['password'] ) )
{
	if ($_POST['username'] == 'admin' && $_POST['password'] == 'password')
	{
		echo "Signed in";
	}
	else
	{
		echo "Error";
	}
}
	