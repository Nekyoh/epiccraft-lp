<?php
/*
 *  Versões do Epic Craft no GitHub Releases.
 *  A consulta é feita pelo servidor e guardada em cache/ (o visitante nunca fala com a API),
 *  então não esbarra no limite de 60 consultas/hora. Se o GitHub estiver fora, usa a
 *  última resposta guardada; se não houver nenhuma, a página mostra só o link de Releases.
 *
 *  Separa dois canais:
 *    estavel → a release mais nova SEM a marca "pre-release"
 *    beta    → a pre-release mais nova, só se for MAIS NOVA que a estável
 *  (rascunhos nunca aparecem: a API não mostra rascunhos para quem não está logado)
 */

function ec_cache_dir(): string {
    $d = __DIR__ . '/../cache';
    if (is_dir($d) && is_writable($d)) return $d;
    $t = rtrim(sys_get_temp_dir(), '/\\') . '/epiccraft-lp';
    if (!is_dir($t)) @mkdir($t, 0700, true);
    return $t;
}

/** GET com timeout curto. Devolve [status, corpo, cabeçalhos]. */
function ec_http_get(string $url, array $headers): array {
    if (function_exists('curl_init')) {
        $resp = [];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$resp) {
                $p = explode(':', $line, 2);
                if (count($p) === 2) $resp[strtolower(trim($p[0]))] = trim($p[1]);
                return strlen($line);
            },
        ]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        return [$body === false ? 0 : $code, $body === false ? '' : $body, $resp];
    }
    $ctx = stream_context_create(['http' => [
        'method' => 'GET', 'header' => implode("\r\n", $headers), 'timeout' => 5, 'ignore_errors' => true,
    ]]);
    $body = @file_get_contents($url, false, $ctx);
    $code = 0; $resp = [];
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) $code = (int)$m[1];
        else { $p = explode(':', $h, 2); if (count($p) === 2) $resp[strtolower(trim($p[0]))] = trim($p[1]); }
    }
    return [$code, $body === false ? '' : $body, $resp];
}

/**
 * ['estavel' => release|null, 'beta' => release|null, 'total' => n] ou null se nunca foi possível consultar.
 * release = ['versao','tag','titulo','data','notas','pagina','beta','jar' => ['nome','url','tamanho']|null]
 */
function ec_versoes(array $cfg): ?array {
    $arq = ec_cache_dir() . '/releases.json';
    $cache = is_file($arq) ? json_decode((string)file_get_contents($arq), true) : null;
    $ttl = (int)($cfg['cache_ttl'] ?? 600);

    if ($cache && isset($cache['quando']) && time() - $cache['quando'] < $ttl) {
        return $cache['versoes'];
    }

    // permite apontar para um servidor de teste: EC_GITHUB_API=http://127.0.0.1:9000
    $api = rtrim(getenv('EC_GITHUB_API') ?: 'https://api.github.com', '/');
    $headers = [
        'Accept: application/vnd.github+json',
        'X-GitHub-Api-Version: 2022-11-28',
        'User-Agent: epiccraft-lp (+https://epiccraft.nekyoh.tech)',
    ];
    $token = getenv('GITHUB_TOKEN') ?: ($cfg['github_token'] ?? '');
    if ($token !== '') $headers[] = 'Authorization: Bearer ' . $token;
    if (!empty($cache['etag'])) $headers[] = 'If-None-Match: ' . $cache['etag'];

    [$code, $body, $resp] = ec_http_get($api . '/repos/' . $cfg['repo'] . '/releases?per_page=30', $headers);

    if ($code === 304 && !empty($cache['versoes'])) {                    // nada mudou (não gasta a cota)
        $cache['quando'] = time();
        @file_put_contents($arq, json_encode($cache), LOCK_EX);
        return $cache['versoes'];
    }
    if ($code === 200 && is_array($j = json_decode($body, true)) && array_is_list($j)) {
        $versoes = ec_separar_canais($j);
        @file_put_contents($arq, json_encode(['quando' => time(), 'etag' => $resp['etag'] ?? '', 'versoes' => $versoes]), LOCK_EX);
        return $versoes;
    }
    // falhou: usa a última resposta boa e tenta de novo daqui a 2 minutos
    $cache = $cache ?: ['etag' => '', 'versoes' => null];
    $cache['quando'] = time() - $ttl + 120;
    @file_put_contents($arq, json_encode($cache), LOCK_EX);
    return $cache['versoes'];
}

function ec_separar_canais(array $lista): array {
    $rels = array_values(array_filter($lista, fn($r) => is_array($r) && empty($r['draft']) && isset($r['tag_name'])));
    // mais nova primeiro (data de publicação)
    usort($rels, fn($a, $b) => strcmp((string)($b['published_at'] ?? ''), (string)($a['published_at'] ?? '')));

    $estavel = null; $beta = null;
    foreach ($rels as $r) {
        if (empty($r['prerelease'])) { $estavel = $r; break; }
        if ($beta === null) $beta = $r;           // pre-release mais nova ANTES da estável = beta mais nova
    }
    return [
        'estavel' => $estavel ? ec_organizar($estavel) : null,
        'beta'    => $beta ? ec_organizar($beta) : null,
        'total'   => count($rels),
    ];
}

function ec_organizar(array $j): array {
    $jar = null;
    foreach ($j['assets'] ?? [] as $a) {
        $n = (string)($a['name'] ?? '');
        // o .jar do mod (não o -sources.jar nem o -javadoc.jar)
        if (preg_match('/\.jar$/i', $n) && !preg_match('/-(sources|javadoc|api|dev)\.jar$/i', $n)) {
            $jar = ['nome' => $n, 'url' => (string)$a['browser_download_url'], 'tamanho' => (int)($a['size'] ?? 0)];
            break;
        }
    }
    $tag = (string)$j['tag_name'];
    return [
        'versao' => ltrim($tag, 'vV'),
        'tag'    => $tag,
        'titulo' => (string)($j['name'] ?: $tag),
        'data'   => (string)($j['published_at'] ?? ''),
        'notas'  => (string)($j['body'] ?? ''),
        'pagina' => (string)($j['html_url'] ?? ''),
        'beta'   => !empty($j['prerelease']),
        'jar'    => $jar,
    ];
}

/* ---------------- Markdown das notas da versão ----------------
 * Só o que as notas usam: títulos, listas, negrito, `código`, links e URLs soltas.
 * Tudo é escapado antes; só http(s) vira link.
 */
function ec_md_inline(string $s): string {
    $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    $s = preg_replace('/`([^`]+)`/', '<code>$1</code>', $s);
    $s = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $s);
    $s = preg_replace('/(?<![\w*])\*(?!\s)(.+?)(?<!\s)\*(?![\w*])/', '<em>$1</em>', $s);
    $s = preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/', fn($m) =>
        '<a href="' . $m[2] . '" target="_blank" rel="noopener">' . $m[1] . '</a>', $s);
    $s = preg_replace_callback('/(?<!href=")(?<!">)(https?:\/\/[^\s<]+[^\s<.,;:)])/', function ($m) {
        $texto = preg_replace('#^https://github\.com/[^/]+/[^/]+/(compare|commits)/#', '', $m[1]);
        return '<a href="' . $m[1] . '" target="_blank" rel="noopener">' . $texto . '</a>';
    }, $s);
    $s = preg_replace('/(^|\s)@([A-Za-z0-9-]{1,39})\b/', '$1<a href="https://github.com/$2" target="_blank" rel="noopener">@$2</a>', $s);
    return $s;
}

function ec_markdown(string $md): string {
    $md = str_replace("\r", '', trim($md));
    if ($md === '') return '';
    $out = []; $lista = false; $par = [];
    $fechaPar = function () use (&$par, &$out) {
        if ($par) { $out[] = '<p>' . implode('<br>', array_map('ec_md_inline', $par)) . '</p>'; $par = []; }
    };
    foreach (explode("\n", $md) as $linha) {
        $l = rtrim($linha);
        if (preg_match('/^\s*[-*+]\s+(.*)$/', $l, $m)) {
            $fechaPar();
            if (!$lista) { $out[] = '<ul>'; $lista = true; }
            $out[] = '<li>' . ec_md_inline($m[1]) . '</li>';
            continue;
        }
        if ($lista) { $out[] = '</ul>'; $lista = false; }
        if (preg_match('/^(#{1,6})\s+(.*)$/', $l, $m)) {
            $fechaPar();
            $n = min(4, strlen($m[1]) + 2);
            $out[] = "<h$n>" . ec_md_inline($m[2]) . "</h$n>";
        } elseif ($l === '' || preg_match('/^(-{3,}|\*{3,})$/', $l)) {
            $fechaPar();
        } else {
            $par[] = $l;
        }
    }
    $fechaPar();
    if ($lista) $out[] = '</ul>';
    return implode("\n", $out);
}
