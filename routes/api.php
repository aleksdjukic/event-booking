<?php

foreach (glob(app_path('Modules/*/Presentation/Routes/api.php')) ?: [] as $routeFile) {
    require $routeFile;
}
