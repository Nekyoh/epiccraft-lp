<?php
require __DIR__ . '/inc/bootstrap.php';

$V      = ec_versoes($CFG);                 // null = GitHub inacessível e sem cache
$est    = $V['estavel'] ?? null;            // última versão estável
$beta   = $V['beta'] ?? null;               // beta mais nova que a estável (se houver)
$atual  = $est ?? $beta;                    // o que a página oferece como principal
$soBeta = !$est && $beta;                   // ainda não saiu nenhuma estável
$L      = $CFG['links'];
$amb    = ['mc' => $CFG['minecraft'], 'loader' => $CFG['loader'], 'java' => $CFG['java']];

// Itens do inventário: id da imagem => raridade (os textos vêm de lang/*.php)
$itens = [
    'monarch_sword' => 'rare', 'warden_sword' => 'epic', 'blaze_sword' => 'common', 'enderman_sword' => 'common',
    'creeper_sword' => 'common', 'firework_sword' => 'common', 'guardian_sword' => 'rare', 'snake_sword' => 'common',
    'wither_rose_sword' => 'common', 'jet' => 'uncommon', 'kunai' => 'common', 'vampiric_dagger' => 'epic',
    'inventor_wrench' => 'rare', 'mjolnir_icon' => 'epic', 'nezonita_scythe' => 'uncommon', 'umbrella' => 'common',
    'frying_pan' => 'common', 'baseball_bat' => 'common', 'bass' => 'uncommon', 'philosofer_stone' => 'epic',
    'nezonita_ingot' => 'uncommon', 'tekynita_ingot' => 'uncommon', 'ruby' => 'common', 'saphire' => 'common',
];
$textos = t('items');
$primeiro = array_key_first($itens);
[$pNome, $pLinhas, $pFonte] = $textos[$primeiro];

$plataformas = [
    'curseforge' => ['CurseForge', $L['curseforge']],
    'modrinth'   => ['Modrinth', $L['modrinth']],
    'github'     => ['GitHub Releases', $atual['pagina'] ?? $L['releases']],
];
$mais = [
    'wiki' => $L['wiki'], 'launcher' => $L['launcher'], 'nekyoh' => $L['nekyoh'], 'issues' => $L['issues'],
];

/** Selo Beta/Estável ao lado de uma versão. */
function selo(?array $r): string {
    if (!$r) return '';
    return $r['beta']
        ? '<span class="selo selo-beta">' . e(t('badge.beta')) . '</span>'
        : '<span class="selo selo-estavel">' . e(t('badge.stable')) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="<?= e(t('html')) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(t('meta.title')) ?></title>
  <meta name="description" content="<?= e(t('meta.desc')) ?>">
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= e(t('meta.title')) ?>">
  <meta property="og:description" content="<?= e(t('meta.desc')) ?>">
  <meta property="og:image" content="res/img/banner.png">
  <meta name="theme-color" content="#0f1610">
  <link rel="icon" type="image/png" href="res/img/favicon.png">
  <?php foreach ($CFG['idiomas'] as $cod => $_): ?>
  <link rel="alternate" hreflang="<?= e($cod) ?>" href="?lang=<?= e($cod) ?>">
  <?php endforeach; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@500;700&family=Rubik:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script src="main.js" defer></script>
</head>
<body>

  <a class="skip" href="#download"><?= e(t('skip')) ?></a>

  <header class="topbar">
    <a class="brand" href="./">
      <img src="res/img/favicon.png" alt="" width="32" height="24">
      <span>Epic Craft</span>
    </a>
    <div class="topbar-right">
      <nav aria-label="Menu">
        <a href="#arsenal"><?= e(t('nav.items')) ?></a>
        <a href="#bosses"><?= e(t('nav.bosses')) ?></a>
        <?php if ($atual): ?><a href="#novidades"><?= e(t('nav.news')) ?></a><?php endif; ?>
        <a class="nav-cta" href="#download"><?= e(t('nav.download')) ?></a>
      </nav>
      <details class="idioma">
        <summary aria-label="<?= e(t('nav.lang')) ?>"><?= e(strtoupper($LANG)) ?></summary>
        <ul>
          <?php foreach ($CFG['idiomas'] as $cod => $nome): ?>
          <li><a href="<?= e(link_idioma($cod)) ?>" hreflang="<?= e($cod) ?>"<?= $cod === $LANG ? ' aria-current="true"' : '' ?>><b><?= e(strtoupper($cod)) ?></b> <?= e($nome) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </details>
    </div>
  </header>

  <main id="top">

    <!-- HERO -->
    <section class="hero">
      <div class="hero-inner">
        <img class="hero-logo" src="res/img/logo.png" alt="Epic Craft" width="785" height="590">
        <div class="hero-copy">
          <p class="kicker"><?= e(t('hero.kicker', $amb)) ?></p>
          <h1><?= e(t('hero.title')) ?></h1>
          <p class="lede"><?= e(t('hero.lede')) ?></p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#download">
              <?= e(t('hero.btn')) ?>
              <?php if ($atual): ?><span class="version">v<?= e($atual['versao']) ?><?= $atual['beta'] ? ' ' . e(t('badge.beta')) : '' ?></span><?php endif; ?>
            </a>
            <a class="btn btn-ghost" href="<?= e($L['github']) ?>" target="_blank" rel="noopener"><?= e(t('hero.src')) ?></a>
          </div>
          <p class="req">
            <?= e(t('hero.req', $amb)) ?><br><?= e(t('hero.langs')) ?>
            <?php if ($soBeta): ?><br><span class="req-beta"><?= e(t('hero.betanote')) ?></span><?php endif; ?>
          </p>
        </div>
      </div>
    </section>

    <!-- ARSENAL -->
    <section id="arsenal" class="band">
      <div class="wrap">
        <h2><?= e(t('arsenal.title')) ?></h2>
        <p class="section-lede"><?= e(t('arsenal.lede')) ?></p>

        <div class="arsenal">
          <div class="gui" role="group" aria-label="<?= e(t('arsenal.gui')) ?>">
            <p class="gui-title">Epic Craft</p>
            <div class="slots" id="slots">
              <?php foreach ($itens as $id => $raridade): [$nome, $linhas, $fonte] = $textos[$id]; ?>
              <button type="button" class="slot<?= $id === $primeiro ? ' is-active' : '' ?>" aria-label="<?= e($nome) ?>"
                      data-id="<?= e($id) ?>" data-rarity="<?= e($raridade) ?>" data-name="<?= e($nome) ?>"
                      data-lines="<?= e(implode("\n", $linhas)) ?>" data-source="<?= e($fonte) ?>">
                <img src="res/img/items/<?= e($id) ?>.png" alt="" width="32" height="32" loading="lazy">
              </button>
              <?php endforeach; ?>
              <?php for ($i = count($itens); $i < 27; $i++): ?><span class="slot slot-empty" aria-hidden="true"></span><?php endfor; ?>
            </div>
          </div>

          <aside class="tooltip" id="tooltip" aria-live="polite">
            <div class="tooltip-head">
              <img id="tt-icon" src="res/img/items/<?= e($primeiro) ?>.png" alt="" width="48" height="48">
              <p class="tt-name" id="tt-name" data-rarity="<?= e($itens[$primeiro]) ?>"><?= e($pNome) ?></p>
            </div>
            <div id="tt-lines"><?php foreach ($pLinhas as $l): ?><p class="tt-line"><?= e($l) ?></p><?php endforeach; ?></div>
            <p class="tt-source" id="tt-source"<?= $pFonte === '' ? ' hidden' : '' ?>><?= e($pFonte) ?></p>
          </aside>
        </div>
      </div>
    </section>

    <!-- CHEFES -->
    <section id="bosses" class="band band-dark">
      <div class="wrap">
        <h2><?= e(t('bosses.title')) ?></h2>
        <p class="section-lede"><?= e(t('bosses.lede')) ?></p>
        <ul class="bosses">
          <?php foreach (['vampire_king', 'the_inventor', 'king_slime'] as $b): ?>
          <li>
            <img src="res/img/mobs/<?= e($b) ?>.png" alt="" width="96" height="96">
            <div>
              <h3><?= e(t("boss.$b.t")) ?></h3>
              <p class="where"><?= e(t("boss.$b.where")) ?></p>
              <p><?= e(t("boss.$b.d")) ?></p>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <!-- MINÉRIOS + ARMADURAS -->
    <section class="band">
      <div class="wrap two-col">
        <div>
          <h2><?= e(t('ores.title')) ?></h2>
          <dl class="ores">
            <?php foreach (['nez' => 'nezonita_ore', 'tek' => 'tekynita_ore', 'gem' => 'ruby_ore'] as $k => $img): ?>
            <div><dt><img src="res/img/items/<?= $img ?>.png" alt="" width="32" height="32"><?= e(t("ore.$k.t")) ?></dt><dd><?= e(t("ore.$k.d")) ?></dd></div>
            <?php endforeach; ?>
          </dl>
        </div>
        <div>
          <h2><?= e(t('armor.title')) ?></h2>
          <dl class="ores">
            <?php foreach (['nez' => 'nezonita_ingot', 'tek' => 'tekynita_ingot', 'stone' => 'philosofer_stone'] as $k => $img): ?>
            <div><dt><img src="res/img/items/<?= $img ?>.png" alt="" width="32" height="32"><?= e(t("armor.$k.t")) ?></dt><dd><?= e(t("armor.$k.d")) ?></dd></div>
            <?php endforeach; ?>
          </dl>
        </div>
      </div>
    </section>

    <!-- DOWNLOAD -->
    <section id="download" class="band band-download">
      <div class="wrap">
        <h2><?= e(t('dl.title')) ?></h2>
        <p class="section-lede">
          <?php if ($est): ?><?= tx('dl.lede.stable', ['v' => $est['versao'], 'd' => ec_data($est['data'])]) ?>
          <?php elseif ($beta): ?><?= tx('dl.lede.beta', ['v' => $beta['versao'], 'd' => ec_data($beta['data'])]) ?>
          <?php else: ?><?= e(t('dl.lede0')) ?><?php endif; ?>
          <?php if ($atual): ?><br><?= e(t('dl.pick')) ?><?php endif; ?>
        </p>

        <?php if ($soBeta): ?>
        <div class="aviso aviso-beta">
          <span class="aviso-selo"><?= e(t('badge.beta')) ?></span>
          <div>
            <h3><?= e(t('beta.only.t')) ?></h3>
            <p><?= e(t('beta.only.d')) ?></p>
          </div>
        </div>
        <?php endif; ?>

        <div class="platforms">
          <?php foreach ($plataformas as $id => [$nome, $url]): ?>
          <?php if ($url): ?>
          <a class="platform" href="<?= e($url) ?>" target="_blank" rel="noopener">
            <span class="platform-name"><?= e($nome) ?></span>
            <span class="platform-note"><?= e(t("plat.$id")) ?></span>
          </a>
          <?php else: ?>
          <span class="platform is-soon" aria-disabled="true">
            <span class="platform-name"><?= e($nome) ?></span>
            <span class="platform-note"><?= e(t('plat.soon')) ?></span>
          </span>
          <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <?php if ($atual && $atual['jar']): ?>
        <div class="direto">
          <a class="btn btn-ghost" href="<?= e($atual['jar']['url']) ?>"><?= e(t('dl.direct')) ?></a>
          <span class="direto-info"><code><?= e($atual['jar']['nome']) ?></code> · <?= e(ec_tamanho($atual['jar']['tamanho'])) ?> <?= selo($atual) ?></span>
        </div>
        <?php elseif (!$atual): ?>
        <div class="direto">
          <a class="btn btn-primary" href="<?= e($L['releases']) ?>" target="_blank" rel="noopener">GitHub Releases</a>
        </div>
        <?php endif; ?>

        <?php if ($est && $beta): ?>
        <div class="aviso aviso-beta">
          <span class="aviso-selo"><?= e(t('badge.beta')) ?></span>
          <div>
            <h3><?= e(t('beta.new.t', ['v' => $beta['versao']])) ?></h3>
            <p><?= e(t('beta.new.d')) ?></p>
            <p class="aviso-acoes">
              <a class="btn btn-ghost btn-sm" href="<?= e($beta['jar']['url'] ?? $beta['pagina']) ?>"><?= e(t('beta.new.btn', ['v' => $beta['versao']])) ?></a>
              <a href="<?= e($beta['pagina']) ?>" target="_blank" rel="noopener"><?= e(t('beta.new.notes')) ?> ↗</a>
            </p>
          </div>
        </div>
        <?php endif; ?>

        <div class="install">
          <h3><?= e(t('install.t')) ?></h3>
          <ol>
            <li><?= tx('install.1', $amb) ?></li>
            <li><?= tx('install.2') ?></li>
            <li><?= tx('install.3') ?></li>
          </ol>
          <p class="install-note"><?= e(t('install.note')) ?>
            <a href="<?= e($L['releases']) ?>" target="_blank" rel="noopener"><?= e(t('dl.all')) ?> ↗</a> ·
            <a href="<?= e($L['github']) ?>" target="_blank" rel="noopener"><?= e(t('dl.source')) ?> ↗</a>
          </p>
        </div>
      </div>
    </section>

    <!-- NOVIDADES -->
    <?php if ($atual): $notas = ec_markdown($atual['notas']); ?>
    <section id="novidades" class="band band-dark">
      <div class="wrap">
        <h2><?= e(t('news.title', ['v' => $atual['versao']])) ?> <?= selo($atual) ?></h2>
        <p class="section-lede"><?= e(ec_data($atual['data'])) ?></p>
        <article class="notas">
          <?= $notas !== '' ? $notas : '<p>' . e(t('news.empty')) . '</p>' ?>
          <p class="notas-mais"><a href="<?= e($L['releases']) ?>" target="_blank" rel="noopener"><?= e(t('news.all')) ?> ↗</a></p>
        </article>
      </div>
    </section>
    <?php endif; ?>

    <!-- MAIS DA NEKYOH -->
    <section id="ecosystem" class="band">
      <div class="wrap">
        <h2><?= e(t('more.title')) ?></h2>
        <div class="links">
          <?php foreach ($mais as $id => $url): ?>
          <?php if ($url): ?>
          <a class="link-row" href="<?= e($url) ?>" target="_blank" rel="noopener">
          <?php else: ?>
          <span class="link-row is-soon" aria-disabled="true">
          <?php endif; ?>
            <span class="link-title"><?= e(t("more.$id.t")) ?></span>
            <span class="link-desc"><?= e($url ? t("more.$id.d") : t('plat.soon')) ?></span>
          <?= $url ? '</a>' : '</span>' ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  </main>

  <footer class="footer">
    <div class="wrap footer-inner">
      <p><?= str_replace(
            ['{tekyoh}', '{nezoru}', '{nekyoh}'],
            ['<a href="' . e($L['tekyoh']) . '" target="_blank" rel="noopener">Tekyoh</a>',
             '<a href="' . e($L['nezoru']) . '" target="_blank" rel="noopener">Nezoru</a>',
             '<a href="' . e($L['nekyoh']) . '" target="_blank" rel="noopener">Nekyoh</a>'],
            e(t('footer.by'))) ?></p>
      <p><?= e(t('footer.lic')) ?> <?= e(t('footer.disc')) ?></p>
    </div>
  </footer>

</body>
</html>
