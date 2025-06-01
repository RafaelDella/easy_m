<?php
session_start();
session_unset();
session_destroy();
header("Location: ../forms_login/1-forms_login.html");
exit;
