/* =========================================================
   LINKS — edit only this block.
   Leave a link as "" while it doesn't exist yet: the button
   is shown as "Coming soon" and can't be clicked.
   ========================================================= */
const LINKS = {
  curseforge: "https://www.curseforge.com/minecraft/mc-mods/epic-craft-mod",
  modrinth:   "",                                   // ex: https://modrinth.com/mod/epiccraft
  github:     "https://github.com/Nekyoh/epiccraft",
  releases:   "https://github.com/Nekyoh/epiccraft/releases",
  issues:     "https://github.com/Nekyoh/epiccraft/issues",
  wiki:       "",                                   // ex: https://wiki.nekyoh.tech
  nekyoh:     "https://nekyoh.tech",
  launcher:   "https://launcher.nekyoh.tech",
};

/* Shown until the latest GitHub release is fetched (or if it fails). */
const FALLBACK_VERSION = "2.0.0";
const GITHUB_REPO = "Nekyoh/epiccraft";

/* =========================================================
   ITEMS — shown in the inventory grid. Text comes from the
   mod's en_us.json tooltips and README.
   rarity: common | uncommon | rare | epic (Minecraft colors)
   ========================================================= */
const ITEMS = [
  { id: "monarch_sword", name: "Monarch Sword", rarity: "rare", lines: ["Charged hits release a huge purple shockwave.", "Bleeding II, Weakness and Slowness. +1 reach."], source: "Crafted with a Nether Star" },
  { id: "warden_sword", name: "Warden Sword", rarity: "epic", lines: ["Hits cause Darkness.", "Hold right-click and release: Sonic Boom."], source: "Dropped by the Warden" },
  { id: "blaze_sword", name: "Blaze Sword", rarity: "common", lines: ["Sets enemies on fire.", "Right-click: shoots 3 fireballs like a Blaze."], source: "Crafted" },
  { id: "enderman_sword", name: "Enderman Sword", rarity: "common", lines: ["Right-click: teleport where you are looking, up to 32 blocks."], source: "Crafted" },
  { id: "creeper_sword", name: "Creeper Sword", rarity: "common", lines: ["Charged hits may explode around the target.", "The blast hurts you a little too!"], source: "Crafted" },
  { id: "firework_sword", name: "Firework Sword", rarity: "common", lines: ["Right-click: launches a firework rocket.", "Hits throw sparks."], source: "Crafted" },
  { id: "guardian_sword", name: "Guardian Sword", rarity: "rare", lines: ["While held: longer breath and faster swimming.", "Hits cause Mining Fatigue and Slowness."], source: "Rare drop from the Elder Guardian (10%)" },
  { id: "snake_sword", name: "Snake Sword", rarity: "common", lines: ["Venomous: Poison II and a chance of Nausea."], source: "Crafted" },
  { id: "wither_rose_sword", name: "Wither Rose Sword", rarity: "common", lines: ["Applies the Wither effect."], source: "Crafted" },

  { id: "jet", name: "Jet", rarity: "uncommon", lines: ["Hook swords: hits pull the enemy in.", "Right-click hooks a mob or pulls you to a block."], source: "Crafted" },
  { id: "kunai", name: "Kunai", rarity: "common", lines: ["Right-click to throw.", "Pick it back up like an arrow. Works in dispensers."], source: "Crafted, 4 at a time" },
  { id: "vampiric_dagger", name: "Vampiric Dagger", rarity: "epic", lines: ["Steals 30% of the damage dealt as health."], source: "Dropped by the Vampire King" },
  { id: "inventor_wrench", name: "Inventor Wrench", rarity: "rare", lines: ["Chance to electrocute the target.", "Right-click a block to rotate it."], source: "Dropped by The Inventor (20%)" },
  { id: "mjolnir_icon", name: "Mjolnir", rarity: "epic", lines: ["Hold right-click and release to throw.", "Lightning strikes! Returns to you like Loyalty."], source: "" },
  { id: "nezonita_scythe", name: "Nezonita Scythe", rarity: "uncommon", lines: ["A scythe forged from Nezonita."], source: "Crafted" },
  { id: "umbrella", name: "Umbrella", rarity: "common", lines: ["Hold it to glide down slowly.", "No fall damage while gliding."], source: "Crafted" },

  { id: "frying_pan", name: "Frying Pan", rarity: "common", lines: ["Works as a shield.", "Hold right-click to block."], source: "Crafted" },
  { id: "baseball_bat", name: "Baseball Bat", rarity: "common", lines: ["Extra knockback.", "Hold right-click to block 50%."], source: "Crafted" },
  { id: "bass", name: "Electric Bass", rarity: "uncommon", lines: ["Play notes in-game.", "Nearby players hear you too."], source: "Crafted" },
  { id: "philosofer_stone", name: "Philosopher's Stone", rarity: "epic", lines: ["Replaces the upgrade template to craft", "Nezonita and Tekynita ingots."], source: "Rare drop from the Vampire King (8%, more with Looting)" },
  { id: "nezonita_ingot", name: "Nezonita Ingot", rarity: "uncommon", lines: ["Full armor set: Strength II, +6 hearts, Fire Resistance."], source: "Smithing: netherite + Philosopher's Stone" },
  { id: "tekynita_ingot", name: "Tekynita Ingot", rarity: "uncommon", lines: ["Full armor set: Speed III, step up 1 block,", "80% less fall damage, Fire Resistance."], source: "Smithing: netherite + Philosopher's Stone" },
  { id: "ruby", name: "Ruby", rarity: "common", lines: ["Tools and armor in a new tier."], source: "Mined from Ruby Ore" },
  { id: "saphire", name: "Saphire", rarity: "common", lines: ["Tools and armor in a new tier."], source: "Mined from Saphire Ore" },
];
const GRID_SIZE = 27; // 3 rows of 9, like a chest

/* ========================================================= */

function applyLinks() {
  document.querySelectorAll("[data-link]").forEach((el) => {
    const url = LINKS[el.dataset.link];
    if (url) {
      el.href = url;
      return;
    }
    // Link not set yet
    el.removeAttribute("href");
    el.setAttribute("aria-disabled", "true");
    el.classList.add("is-soon");
    if (!el.closest(".topbar")) {
      const note = el.querySelector(".platform-note, .link-desc");
      if (note) note.textContent = "Coming soon";
    }
  });
  // Nav "Wiki" without a URL: scroll to the links section instead
  const navWiki = document.querySelector('.topbar [data-link="wiki"]');
  if (navWiki && !LINKS.wiki) {
    navWiki.href = "#ecosystem";
    navWiki.removeAttribute("aria-disabled");
    navWiki.classList.remove("is-soon");
  }
}

function setVersion(v) {
  const label = v.startsWith("v") ? v : "v" + v;
  document.querySelectorAll("[data-version]").forEach((el) => (el.textContent = label));
}

async function fetchVersion() {
  setVersion(FALLBACK_VERSION);
  try {
    const res = await fetch(`https://api.github.com/repos/${GITHUB_REPO}/releases/latest`, {
      headers: { Accept: "application/vnd.github+json" },
    });
    if (!res.ok) return;
    const data = await res.json();
    if (data.tag_name) setVersion(data.tag_name.replace(/^.*?(\d)/, "$1"));
  } catch (_) {
    /* offline or rate-limited: keep fallback */
  }
}

function buildInventory() {
  const slots = document.getElementById("slots");
  const tt = {
    box: document.getElementById("tooltip"),
    icon: document.getElementById("tt-icon"),
    name: document.getElementById("tt-name"),
    lines: document.getElementById("tt-lines"),
    source: document.getElementById("tt-source"),
  };

  const show = (item, btn) => {
    tt.icon.src = `res/img/items/${item.id}.png`;
    tt.name.textContent = item.name;
    tt.name.dataset.rarity = item.rarity;
    tt.lines.replaceChildren(
      ...item.lines.map((l) => {
        const p = document.createElement("p");
        p.className = "tt-line";
        p.textContent = l;
        return p;
      })
    );
    tt.source.textContent = item.source;
    tt.source.hidden = !item.source;
    slots.querySelectorAll(".slot.is-active").forEach((s) => s.classList.remove("is-active"));
    btn.classList.add("is-active");
  };

  let first = null;
  ITEMS.forEach((item) => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "slot";
    btn.setAttribute("aria-label", item.name);
    const img = document.createElement("img");
    img.src = `res/img/items/${item.id}.png`;
    img.alt = "";
    img.width = 32;
    img.height = 32;
    img.loading = "lazy";
    btn.append(img);
    btn.addEventListener("mouseenter", () => show(item, btn));
    btn.addEventListener("focus", () => show(item, btn));
    btn.addEventListener("click", () => show(item, btn));
    slots.append(btn);
    if (!first) first = [item, btn];
  });
  for (let i = ITEMS.length; i < GRID_SIZE; i++) {
    const empty = document.createElement("span");
    empty.className = "slot slot-empty";
    empty.setAttribute("aria-hidden", "true");
    slots.append(empty);
  }
  if (first) show(...first);
}

applyLinks();
fetchVersion();
buildInventory();
