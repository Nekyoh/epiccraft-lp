/* Tooltip do inventário: troca o item mostrado ao passar o mouse, focar ou tocar num slot.
   Os textos já vêm traduzidos do servidor nos atributos data-* de cada slot.
   Sem JavaScript a página funciona igual, só fica mostrando o primeiro item. */
(function () {
  const slots = document.getElementById("slots");
  if (!slots) return;
  const icon = document.getElementById("tt-icon");
  const name = document.getElementById("tt-name");
  const lines = document.getElementById("tt-lines");
  const source = document.getElementById("tt-source");

  function show(btn) {
    const d = btn.dataset;
    icon.src = "res/img/items/" + d.id + ".png";
    name.textContent = d.name;
    name.dataset.rarity = d.rarity;
    lines.replaceChildren(...d.lines.split("\n").filter(Boolean).map((l) => {
      const p = document.createElement("p");
      p.className = "tt-line";
      p.textContent = l;
      return p;
    }));
    source.textContent = d.source;
    source.hidden = !d.source;
    slots.querySelectorAll(".slot.is-active").forEach((s) => s.classList.remove("is-active"));
    btn.classList.add("is-active");
  }

  slots.querySelectorAll("button.slot").forEach((btn) => {
    ["mouseenter", "focus", "click"].forEach((ev) => btn.addEventListener(ev, () => show(btn)));
  });
})();
