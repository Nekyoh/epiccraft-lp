<?php
/*
 *  Links fixos que sempre levam ao .jar mais novo:
 *    https://epiccraft.nekyoh.tech/download          → versão estável (ou a beta, se ainda não houver estável)
 *    https://epiccraft.nekyoh.tech/download/beta     → beta mais nova (ou a estável, se não houver beta mais nova)
 *  (sem mod_rewrite: download.php?c=beta)
 */
$CFG = require __DIR__ . '/config.php';
require __DIR__ . '/inc/github.php';

$v = ec_versoes($CFG);
$canal = strtolower((string)($_GET['c'] ?? ''));
$rel = $canal === 'beta'
    ? ($v['beta'] ?? $v['estavel'] ?? null)
    : ($v['estavel'] ?? $v['beta'] ?? null);
$url = $rel['jar']['url'] ?? ($rel['pagina'] ?? $CFG['links']['releases']);

header('Cache-Control: no-store');
header('Location: ' . $url, true, 302);
