<?php
if (version_compare(\Pest\version(), "3.0.0") >= 0) {
    pest()->group('plugins');
}
