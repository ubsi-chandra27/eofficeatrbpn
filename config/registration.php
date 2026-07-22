<?php

return [
    // Jangan aktifkan pada situs publik. Akun staf sebaiknya dibuat oleh Admin.
    'allow_staff' => (bool) env('ALLOW_STAFF_REGISTRATION', false),
];
