<?php
/*
 * ============================================================
 *  Epic Craft — site do mod (epiccraft.nekyoh.tech)
 *  Links e opções ficam aqui. Os TEXTOS ficam em lang/*.php
 * ============================================================
 */
return [
    'nome'    => 'Epic Craft',
    // Endereço público do site (usado no link canônico e no sitemap)
    'url'     => 'https://epiccraft.nekyoh.tech',

    'autores' => ['Tekyoh', 'Nezoru'],

    // Repositório no GitHub de onde vêm as versões, o .jar e as novidades
    'repo' => 'Nekyoh/epiccraft',

    // Versões do jogo (aparecem na página)
    'minecraft' => '1.21.1',
    'loader'    => 'NeoForge 21.1',
    'java'      => '21',

    // Quanto tempo (segundos) guardar a resposta do GitHub antes de perguntar de novo.
    'cache_ttl' => 600,

    // Opcional: token do GitHub para subir o limite de consultas.
    // Prefira a variável de ambiente GITHUB_TOKEN no servidor.
    'github_token' => '',

    'idiomas'       => ['pt' => 'Português', 'en' => 'English', 'es' => 'Español'],
    'idioma_padrao' => 'en',

    // Deixe '' enquanto o link não existir: o botão aparece como "Em breve".
    'links' => [
        'curseforge' => 'https://www.curseforge.com/minecraft/mc-mods/epic-craft-nekyoh',
        'modrinth'   => 'https://modrinth.com/mod/epiccraft-nekyoh',
        'github'     => 'https://github.com/Nekyoh/epiccraft',
        'releases'   => 'https://github.com/Nekyoh/epiccraft/releases',
        'issues'     => 'https://github.com/Nekyoh/epiccraft/issues',
        'wiki'       => '',                       // ex.: https://wiki.nekyoh.tech
        'nekyoh'     => 'https://nekyoh.tech',
        'launcher'   => 'https://launcher.nekyoh.tech',
        'neoforge'   => 'https://neoforged.net',
        'tekyoh'     => 'https://www.youtube.com/@Tekyoh01',
        'nezoru'     => 'https://www.youtube.com/@Nezoru',
    ],
];
