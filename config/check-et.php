<?php

// config/check-et.php
return [
    "api_key" => env("CHECK_ET_API_KEY", "Test-1234"),
    "base_url" => env("CHECK_ET_BASE_URL", "https://api.check.et/api/v1"),
    "timeout" => env("CHECK_ET_TIMEOUT", 30),
    "branch_id" => env("CHECK_ET_BRANCH_ID", null),
];
