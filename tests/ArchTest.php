<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

if (version_compare(\Pest\version(), "3.0.0") >= 0) {
    arch()->preset()->php();

    arch()->preset()->security();
}
