<?php
/**
 * Shared title component scaffold for Kitchens, Blog, and future archives.
 *
 * NOTE: Not yet used anywhere — safe placeholder.
 *
 * Expected usage (future):
 * get_template_part('template-parts/components/shared-title', null, [
 *      'context' => 'kitchens',   // or 'blog'
 *      'params'  => [ 'search' => 'xyz', ... ]
 * ]);
 */

$context = $args['context'] ?? '';
$params  = $args['params']  ?? [];

// This file intentionally left minimal — the actual logic will be
// introduced once we start consolidating title behaviour.
?>
<div class="shared-title-placeholder hidden">
    <!-- Placeholder component – not active yet -->
</div>

