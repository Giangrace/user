<?php
session_start(); // ✅ Must start session first!
session_destroy();
header("Location: index.html"); // Changed to index.html since that's your login page
exit(); // ✅ Always add exit after header redirect
?>