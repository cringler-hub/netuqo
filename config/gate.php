<?php

return [
    /*
     * Temporary site-wide password gate protecting the whole app until real login
     * (username/password + Google/Microsoft/Apple) ships — see DECISIONS.md and
     * ROADMAP.md. Not per-user authentication: everyone who knows this one password
     * gets in. Empty/unset fails closed — nobody can unlock the gate.
     */
    'password' => env('GATE_PASSWORD'),
];
