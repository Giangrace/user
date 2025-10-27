<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
```

## **Visual Result:**
The navigation will look like:
```
Home | About | Profile | Service | Project    [🚪 Logout]